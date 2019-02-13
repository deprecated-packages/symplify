<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Printer;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;

final class PhpDocInfoPrinter
{
    /**
     * @var int
     */
    private $tokenCount;

    /**
     * @var int
     */
    private $currentTokenPosition;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var int[][]
     */
    private $removedNodePositions = [];

    /**
     * @var PhpDocNode
     */
    private $phpDocNode;

    /**
     * @var PhpDocNode
     */
    private $originalPhpDocNode;

    /**
     * @var OriginalSpacingRestorer
     */
    private $originalSpacingRestorer;

    /**
     * @var PhpDocInfo
     */
    private $phpDocInfo;

    public function __construct(OriginalSpacingRestorer $originalSpacingRestorer)
    {
        $this->originalSpacingRestorer = $originalSpacingRestorer;
    }

    /**
     * As in php-parser
     *
     * ref: https://github.com/nikic/PHP-Parser/issues/487#issuecomment-375986259
     * - Tokens[node.startPos .. subnode1.startPos]
     * - Print(subnode1)
     * - Tokens[subnode1.endPos .. subnode2.startPos]
     * - Print(subnode2)
     * - Tokens[subnode2.endPos .. node.endPos]
     */
    public function printFormatPreserving(PhpDocInfo $phpDocInfo): string
    {
        $this->phpDocNode = $phpDocInfo->getPhpDocNode();
        $this->originalPhpDocNode = $phpDocInfo->getOriginalPhpDocNode();
        $this->tokens = $phpDocInfo->getTokens();
        $this->tokenCount = count($phpDocInfo->getTokens());
        $this->phpDocInfo = $phpDocInfo;

        $this->currentTokenPosition = 0;
        $this->removedNodePositions = [];

        return $this->printPhpDocNode($this->phpDocNode);
    }

    private function printPhpDocNode(PhpDocNode $phpDocNode): string
    {
        // no nodes were, so empty doc
        if ($this->isPhpDocNodeEmpty($phpDocNode)) {
            return '';
        }

        $this->currentTokenPosition = 0;

        $output = '';

        // node output
        $nodeCount = count($phpDocNode->children);
        foreach ($phpDocNode->children as $i => $child) {
            $output .= $this->printNode($child, null, $i + 1, $nodeCount);
        }

        return $this->printEnd($output);
    }

    private function isPhpDocNodeEmpty(PhpDocNode $phpDocNode): bool
    {
        if (count($phpDocNode->children) === 0) {
            return true;
        }

        foreach ($phpDocNode->children as $phpDocChildNode) {
            if ($phpDocChildNode instanceof PhpDocTextNode) {
                if ($phpDocChildNode->text) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    private function printNode(
        Node $node,
        ?int $startTokenPosition = null,
        ?int $endTokenPosition = null,
        int $i = 0,
        int $nodeCount = 0
    ): string {
        $output = '';

        // tokens before
        if ($node instanceof AttributeAwareNodeInterface) {
            /** @var int|null $startTokenPosition */
            $startTokenPosition = $node->getAttribute(Attribute::START_TOKEN_POSITION) ?: $startTokenPosition;
            /** @var int|null $endTokenPosition */
            $endTokenPosition = $node->getAttribute(Attribute::END_TOKEN_POSITION) ?: $endTokenPosition;
        }

        if ($startTokenPosition && $endTokenPosition) {
            $isLastToken = ($nodeCount === $i);

            $output = $this->addTokensFromTo(
                $output,
                $this->currentTokenPosition,
                $startTokenPosition,
                $isLastToken
            );

            $this->currentTokenPosition = $endTokenPosition;
        }

        if ($node instanceof PhpDocTagNode && $startTokenPosition) {
            return $this->printPhpDocTagNode($node, $startTokenPosition, $endTokenPosition, $output);
        }

        if (! $node instanceof PhpDocTextNode && ! $node instanceof GenericTagValueNode && $startTokenPosition && $endTokenPosition) {
            return $this->originalSpacingRestorer->restoreInOutputWithTokensStartAndEndPosition(
                (string) $node,
                $this->tokens,
                $startTokenPosition,
                $endTokenPosition
            );
        }

        if ($node instanceof PhpDocTagNode) {
            return $output . PHP_EOL . '     * ' . (string) $node;
        }

        return $output . (string) $node;
    }

    private function printEnd(string $output): string
    {
        return $this->addTokensFromTo($output, $this->getLastNodeTokenEndPosition(), $this->tokenCount, true);
    }

    private function addTokensFromTo(
        string $output,
        int $from,
        int $to,
        bool $shouldSkipEmptyLinesAbove = false
    ): string {
        // skip removed nodes
        $positionJumpSet = [];
        foreach ($this->getRemovedNodesPositions() as [$startPosition, $endPosition]) {
            $positionJumpSet[$startPosition] = $endPosition;
        }

        // include also space before, in case of inlined docs
        if (isset($this->tokens[$from - 1]) && $this->tokens[$from - 1][1] === Lexer::TOKEN_HORIZONTAL_WS) {
            --$from;
        }

        if ($shouldSkipEmptyLinesAbove) {
            // skip extra empty lines above if this is the last one
            if (Strings::contains($this->tokens[$from][0], PHP_EOL) && Strings::contains(
                $this->tokens[$from + 1][0],
                PHP_EOL
            )) {
                ++$from;
            }
        }

        return $this->appendToOutput($output, $from, $to, $positionJumpSet);
    }

    private function printPhpDocTagNode(
        PhpDocTagNode $phpDocTagNode,
        int $startTokenPosition,
        int $endTokenPosition,
        string $output
    ): string {
        $output .= $phpDocTagNode->name;

        $nodeOutput = $this->printNode($phpDocTagNode->value, $startTokenPosition, $endTokenPosition);

        if ($nodeOutput && $this->isTagSeparatedBySpace($nodeOutput, $phpDocTagNode)) {
            $output .= ' ';
        }

        return $output . $nodeOutput;
    }

    /**
     * @todo consider some position storage
     */
    private function getLastNodeTokenEndPosition(): int
    {
        $originalChildren = $this->originalPhpDocNode->children;
        if ($originalChildren === []) {
            return $this->currentTokenPosition;
        }

        $lastOriginalChildrenNode = array_pop($originalChildren);

        if ($lastOriginalChildrenNode instanceof AttributeAwareNodeInterface) {
            /** @var int|null $endTokenPosition */
            $endTokenPosition = $lastOriginalChildrenNode->getAttribute(Attribute::END_TOKEN_POSITION);

            if ($endTokenPosition !== null) {
                return $endTokenPosition;
            }
        }

        return $this->currentTokenPosition;
    }

    /**
     * @return int[][]
     */
    private function getRemovedNodesPositions(): array
    {
        if ($this->removedNodePositions) {
            return $this->removedNodePositions;
        }

        $removedNodes = array_diff($this->originalPhpDocNode->children, $this->phpDocNode->children);

        $removedNodesPositions = [];
        foreach ($removedNodes as $removedNode) {
            if ($removedNode instanceof AttributeAwareNodeInterface) {
                $removedStartTokenPosition = $removedNode->getAttribute(Attribute::START_TOKEN_POSITION);

                // change start position to start of the line, so the whole line is removed
                $seekPosition = $removedStartTokenPosition;
                while ($this->tokens[$seekPosition][1] !== Lexer::TOKEN_HORIZONTAL_WS) {
                    --$seekPosition;
                }

                $removedNodesPositions[] = [
                    $seekPosition - 1,
                    $removedNode->getAttribute(Attribute::END_TOKEN_POSITION),
                ];
            }
        }

        return $this->removedNodePositions = $removedNodesPositions;
    }

    /**
     * @param int[] $positionJumpSet
     */
    private function appendToOutput(string $output, int $from, int $to, array $positionJumpSet): string
    {
        for ($i = $from; $i < $to; ++$i) {
            while (isset($positionJumpSet[$i])) {
                $i = $positionJumpSet[$i];
            }

            $output .= $this->tokens[$i][0] ?? '';
        }

        return $output;
    }

    /**
     * Covers:
     * - "@Long\Annotation"
     * - "@Route("/", name="homepage")",
     * - "@customAnnotation(value)"
     */
    private function isTagSeparatedBySpace(string $nodeOutput, PhpDocTagNode $phpDocTagNode): bool
    {
        $contentWithoutSpace = $phpDocTagNode->name . $nodeOutput;
        if (Strings::contains($this->phpDocInfo->getOriginalContent(), $contentWithoutSpace)) {
            return false;
        }

        if (Strings::contains($this->phpDocInfo->getOriginalContent(), $phpDocTagNode->name . ' ')) {
            return true;
        }

        return false;
    }
}

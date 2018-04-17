<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Lexer\Lexer as PHPStanLexer;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Ast\Type\FormatPreservingUnionTypeNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Storage\NodeWithPositionsObjectStorage;

final class PhpDocInfoPrinter
{
    /**
     * @var NodeWithPositionsObjectStorage
     */
    private $nodeWithPositionsObjectStorage;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var int
     */
    private $tokenCount;

    /**
     * @var int
     */
    private $currentTokenPosition;

    /**
     * @var PhpDocNode
     */
    private $originalPhpDocNode;

    public function __construct(NodeWithPositionsObjectStorage $nodeWithPositionsObjectStorage)
    {
        $this->nodeWithPositionsObjectStorage = $nodeWithPositionsObjectStorage;
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
        $phpDocNode = $phpDocInfo->getPhpDocNode();
        $this->originalPhpDocNode = clone $phpDocInfo->getPhpDocNode();
        $this->tokens = $phpDocInfo->getTokens();
        $this->tokenCount = count($phpDocInfo->getTokens());

        return $this->printPhpDocNode($phpDocNode);
    }

    private function printPhpDocNode(PhpDocNode $phpDocNode): string
    {
        $this->currentTokenPosition = 0;
        $output = '';

        foreach ($phpDocNode->children as $child) {
            $output .= $this->printNode($child);
        }

        // no nodes => empty output? we need to render start
        if ($output === '') {
            $offset = -1;
            if ($this->tokens[$this->getFirstNodeStartPosition()][1] === PHPStanLexer::TOKEN_PHPDOC_EOL) {
                $offset = 0;
            }

            for ($i = 0; $i < $this->getFirstNodeStartPosition() - $offset; ++$i) {
                if (isset($this->tokens[$i])) {
                    $output .= $this->tokens[$i][0];
                }
            }
        }

        // tokens after - only for the last Node
        $offset = 1;

        if ($this->tokens[$this->currentTokenPosition][1] === PHPStanLexer::TOKEN_PHPDOC_EOL) {
            $offset = 0;
        }

        for ($i = $this->getLastNodeTokenEndPosition() - $offset; $i < $this->tokenCount; ++$i) {
            if (isset($this->tokens[$i])) {
                $output .= $this->tokens[$i][0];
            }
        }

        return $output;
    }

    /**
     * @param int[] $nodePositions
     */
    private function printNode(Node $node, array $nodePositions = []): string
    {
        $output = '';
        // tokens before
        if (isset($this->nodeWithPositionsObjectStorage[$node])) {
            $nodePositions = $this->nodeWithPositionsObjectStorage[$node];
            for ($i = $this->currentTokenPosition; $i < $nodePositions['tokenStart']; ++$i) {
                $output .= $this->tokens[$i][0];
            }

            $this->currentTokenPosition = $nodePositions['tokenEnd'];
        }

        // @todo recurse
        if ($node instanceof PhpDocTagNode) {
            return $this->printPhpDocTagNode($node, $nodePositions, $output);
        }

        if ($node instanceof ParamTagValueNode) {
            return $this->keepLineOriginalSpaces($nodePositions, (string) $node);
        }

        return $output . (string) $node;
    }

    /**
     * @param mixed[] $nodePositions
     */
    private function printPhpDocTagNode(PhpDocTagNode $phpDocTagNode, array $nodePositions, string $output): string
    {
        $output .= $phpDocTagNode->name;
        $output .= ' '; // @todo not manually

        if ($phpDocTagNode->value instanceof ParamTagValueNode || $phpDocTagNode->value instanceof ReturnTagValueNode || $phpDocTagNode->value instanceof VarTagValueNode) {
            if ($phpDocTagNode->value->type instanceof UnionTypeNode) {
                // @todo temp workaround
                $nodeValueType = $phpDocTagNode->value->type;
                /** @var UnionTypeNode $nodeValueType */
                $phpDocTagNode->value->type = new FormatPreservingUnionTypeNode($nodeValueType->types);
            }
        }

        return $output . $this->printNode($phpDocTagNode->value, $nodePositions);
    }

    /**
     * @param string[] $nodePositions
     */
    private function keepLineOriginalSpaces(array $nodePositions, string $nodeOutput): string
    {
        $oldWhitespaces = [];
        for ($i = $nodePositions['tokenStart']; $i < $nodePositions['tokenEnd']; ++$i) {
            if ($this->tokens[$i][1] === Lexer::TOKEN_HORIZONTAL_WS) {
                $oldWhitespaces[] = $this->tokens[$i][0];
            }
        }

        // no original whitespaces, return
        if (! $oldWhitespaces) {
            return $nodeOutput;
        }

        // first space is not covered by this
        array_shift($oldWhitespaces);

        // get all old whitespaces
        $newWhitespaces = Strings::match($nodeOutput, '#\s+#');

        // replace system whitespace by old ones
        return (string) str_replace($newWhitespaces, $oldWhitespaces, $nodeOutput);
    }

    /**
     * @todo consider some position storage
     */
    private function getLastNodeTokenEndPosition(): int
    {
        $originalChildren = $this->originalPhpDocNode->children;
        if (! $originalChildren) {
            return $this->currentTokenPosition;
        }

        $lastOriginalChildrenNode = array_pop($originalChildren);
        if (! $lastOriginalChildrenNode) {
            return $this->currentTokenPosition;
        }

        if (! isset($this->nodeWithPositionsObjectStorage[$lastOriginalChildrenNode])) {
            return $this->currentTokenPosition;
        }

        return $this->nodeWithPositionsObjectStorage[$lastOriginalChildrenNode]['tokenEnd'] + 1;
    }

    private function getFirstNodeStartPosition(): int
    {
        $originalChildren = $this->originalPhpDocNode->children;
        if (! $originalChildren) {
            return $this->currentTokenPosition;
        }

        $firstOriginalChildrenNode = array_shift($originalChildren);
        if (! $firstOriginalChildrenNode) {
            return $this->currentTokenPosition;
        }

        if (! isset($this->nodeWithPositionsObjectStorage[$firstOriginalChildrenNode])) {
            return $this->currentTokenPosition;
        }

        return $this->nodeWithPositionsObjectStorage[$firstOriginalChildrenNode]['tokenStart'] - 2;
    }
}

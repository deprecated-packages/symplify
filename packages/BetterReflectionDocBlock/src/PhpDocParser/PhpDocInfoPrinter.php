<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
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

        // tokens after - only for the last Node
        $offset = 1;

        if ($this->tokens[$this->currentTokenPosition][1] === PHPStanLexer::TOKEN_PHPDOC_EOL) {
            $offset = 0;
        }

        for ($i = $this->currentTokenPosition - $offset; $i < $this->tokenCount; ++$i) {
            if (isset($this->tokens[$i])) {
                $output .= $this->tokens[$i][0];
            }
        }

        return $output;
    }

    private function printNode(Node $node): string
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
            $output .= $node->name;
            $output .= ' '; // @todo not manually

            if ($node->value instanceof ParamTagValueNode) {
                if ($node->value->type instanceof UnionTypeNode) {
                    // @todo temp workaround
                    $nodeValueType = $node->value->type;
                    /** @var UnionTypeNode $nodeValueType */
                    $node->value->type = new FormatPreservingUnionTypeNode($nodeValueType->types);
                }
            }

            return $output . $this->printNode($node->value);
        }

        return $output . (string) $node;
    }
}

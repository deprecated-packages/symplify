<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer as PHPStanLexer;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Ast\Type\FormatPreservingUnionTypeNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Storage\NodeWithPositionsObjectStorage;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class PhpDocInfoPrinter
{
    /**
     * @var NodeWithPositionsObjectStorage
     */
    private $nodeWithPositionsObjectStorage;

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var int
     */
    private $tokenCount;

    public function __construct(NodeWithPositionsObjectStorage $nodeWithPositionsObjectStorage)
    {
        $this->privatesAccessor = new PrivatesAccessor();
        $this->nodeWithPositionsObjectStorage = $nodeWithPositionsObjectStorage;
    }

//        foreach ($phpDocNode->children as $childNode) {
//            if ($childNode instanceof PhpDocTextNode && $childNode->text === '') {
//                $middle .= ' *' . PHP_EOL;
//            } else {
//                if ($this->hasUnionType($childNode)) {
//                    /** @var PhpDocTagNode $childNode */
//                    $childNodeValue = $childNode->value;
//                    /** @var ParamTagValueNode $childNodeValue */
//                    $childNodeValueType = $childNodeValue->type;
//                    /** @var UnionTypeNode $childNodeValueType */
//                    // @todo: here it requires to check format of original node, as in PHPParser
//                    $childNodeValue->type = new FormatPreservingUnionTypeNode($childNodeValueType->types);
//                }
//
//                $middle .= ' * ' . (string) $childNode . PHP_EOL;
//            }
//        }

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

        $output = $this->printPhpDocNode($phpDocNode);

        return $output;
    }

    private function hasUnionType(PhpDocChildNode $phpDocChildNode): bool
    {
        if (! $phpDocChildNode instanceof PhpDocTagNode) {
            return false;
        }

        if (! $phpDocChildNode->value instanceof ParamTagValueNode) {
            return false;
        }

        return $phpDocChildNode->value->type instanceof UnionTypeNode;
    }

    /**
     * @param mixed[] $tokens
     */
    private function printPhpDocNode(PhpDocNode $phpDocNode): string
    {
        $tokenPosition = 0;
        $output = '';
        foreach ($phpDocNode->children as $child) {
            [$tokenPosition, $newOutput] = $this->printNode($child, $tokenPosition);
            $output .= $newOutput;
        }

        // tokens after - only for the last Node
        $offset = 1;

        if ($this->tokens[$tokenPosition][1] === PHPStanLexer::TOKEN_PHPDOC_EOL) {
            $offset = 0;
        }

        for ($i = $tokenPosition - $offset; $i < $this->tokenCount; ++$i) {
            $output .= $this->tokens[$i][0];
        }

        return $output;
    }

    /**
     * @return mixed[]
     */
    private function printNode(Node $node, int $tokenPosition): array
    {
        $output = '';
        // tokens before
        if (isset($this->nodeWithPositionsObjectStorage[$node])) {
            $nodePositions = $this->nodeWithPositionsObjectStorage[$node];
            for ($i = $tokenPosition; $i < $nodePositions['tokenStart']; ++$i) {
                $output .= $this->tokens[$i][0];
            }

            $tokenPosition = $nodePositions['tokenEnd'];
        }

        // @todo recurse
        $output .= (string) $node;

        return [$tokenPosition, $output];
    }
}

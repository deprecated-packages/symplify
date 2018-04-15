<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Ast\Type\FormatPreservingUnionTypeNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Storage\NodeWithPositionsObjectStorage;
use Symplify\PackageBuilder\Reflection\PrivatesGetter;

final class PhpDocInfoPrinter
{
    /**
     * @var NodeWithPositionsObjectStorage
     */
    private $nodeWithPositionsObjectStorage;

    /**
     * @var PrivatesGetter
     */
    private $privatesGetter;

    public function __construct(NodeWithPositionsObjectStorage $nodeWithPositionsObjectStorage)
    {
        $this->privatesGetter = new PrivatesGetter();
        $this->nodeWithPositionsObjectStorage = $nodeWithPositionsObjectStorage;
    }

    public function print(PhpDocInfo $phpDocInfo): string
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        if ($phpDocInfo->isSingleLineDoc()) {
            return sprintf('/** %s */', implode(' ', $phpDocNode->children));
        }

        $start = '/**' . PHP_EOL;
        $end = ' */';

        $middle = '';
        foreach ($phpDocNode->children as $childNode) {
            if ($childNode instanceof PhpDocTextNode && $childNode->text === '') {
                $middle .= ' *' . PHP_EOL;
            } else {
                if ($this->hasUnionType($childNode)) {
                    /** @var PhpDocTagNode $childNode */
                    $childNodeValue = $childNode->value;
                    /** @var ParamTagValueNode $childNodeValue */
                    $childNodeValueType = $childNodeValue->type;
                    /** @var UnionTypeNode $childNodeValueType */
                    // @todo: here it requires to check format of original node, as in PHPParser
                    $childNodeValue->type = new FormatPreservingUnionTypeNode($childNodeValueType->types);
                }

                $middle .= ' * ' . (string) $childNode . PHP_EOL;
            }
        }

        return $start . $middle . $end;
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
        $newNode = $phpDocInfo->getPhpDocNode();

        $tokens = $this->privatesGetter->getPrivateProperty($phpDocInfo->getTokenIterator(), 'tokens');

        $tokenPosition = 0;
        $output = '';
        foreach ($newNode->children as $child) {
            // tokens before
            if (isset($this->nodeWithPositionsObjectStorage[$child])) {
                $nodePositions = $this->nodeWithPositionsObjectStorage[$child];
                for ($i = $tokenPosition; $i < $nodePositions['tokenStart']; ++$i) {
                    $output .= $tokens[$i][0];
                }

                $tokenPosition = $nodePositions['tokenEnd'];
            }

            // @todo recurse
            $output .= (string) $child;
        }

        // tokens after - only for the last Node
        for ($i = $tokenPosition - 1; $i < count($tokens); ++$i) {
            $output .= $tokens[$i][0];
        }

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
}

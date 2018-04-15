<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Ast\Type\FormatPreservingUnionTypeNode;

final class PhpDocInfoPrinter
{
    public function print(PhpDocInfo $phpDocInfo): string
    {
        $phpDocNode = $phpDocInfo->getPhpDocNode();

        // no changes
        $tokenIterator = $phpDocInfo->getTokenIterator();

        // https://github.com/nikic/PHP-Parser/issues/487#issuecomment-375986259

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
        // each node has to include token start and end position
        $newNode = $phpDocInfo->getPhpDocNode();
        $oldNode = $phpDocInfo->getOldPhpDocNode();
        $oldTokens = $phpDocInfo->getTokenIterator();

        return '';
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

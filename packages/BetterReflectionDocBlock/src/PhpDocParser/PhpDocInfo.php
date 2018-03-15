<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\Ast\Type\FormatPreservingUnionTypeNode;

final class PhpDocInfo
{
    /**
     * @var PhpDocNode
     */
    private $phpDocNode;

    /**
     * @var bool
     */
    private $isSingleLineDoc;

    public function __construct(PhpDocNode $phpDocNode, bool $isSingleLineDoc)
    {
        $this->phpDocNode = $phpDocNode;
        $this->isSingleLineDoc = $isSingleLineDoc;
    }

    public function __toString(): string
    {
        if ($this->isSingleLineDoc) {
            return sprintf('/** %s */', implode(' ', $this->phpDocNode->children));
        }

        $start = '/**' . PHP_EOL;
        $end = ' */' . PHP_EOL;

        $middle = '';
        foreach ($this->phpDocNode->children as $childNode) {
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

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
    }

    public function isSingleLineDoc(): bool
    {
        return $this->isSingleLineDoc;
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

<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type;

use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;

final class AttributeAwareUnionTypeNode extends UnionTypeNode implements AttributeAwareNodeInterface
{
    use AttributeTrait;

    /**
     * Preserve common format
     */
    public function __toString(): string
    {
        return implode('|', $this->types);
    }
}

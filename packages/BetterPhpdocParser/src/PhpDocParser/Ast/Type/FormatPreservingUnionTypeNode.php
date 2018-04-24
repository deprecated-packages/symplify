<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser\Ast\Type;

use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;

final class FormatPreservingUnionTypeNode extends UnionTypeNode
{
    public function __toString(): string
    {
        return implode('|', $this->types);
    }
}

<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser\Ast\Type;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;

final class FormatPreservingUnionTypeNode implements TypeNode
{
    /**
     * @var TypeNode[]
     */
    public $types = [];

    /**
     * @param TypeNode[] $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    public function __toString(): string
    {
        return implode('|', $this->types);
    }
}

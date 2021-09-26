<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

use PHPStan\Type\Type;

final class ComponentNameAndType
{
    public function __construct(
        private string $name,
        private Type $returnType
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): Type
    {
        return $this->returnType;
    }
}

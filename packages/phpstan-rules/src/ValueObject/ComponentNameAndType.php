<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ComponentNameAndType
{
    public function __construct(
        private string $name,
        private \PHPStan\Type\Type $returnType
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): \PHPStan\Type\Type
    {
        return $this->returnType;
    }
}

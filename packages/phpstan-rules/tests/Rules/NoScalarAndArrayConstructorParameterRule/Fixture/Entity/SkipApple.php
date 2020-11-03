<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture\Entity;

final class SkipApple
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

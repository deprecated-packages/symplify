<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\Fixture;

use Symfony\Component\Console\Command\Command;

final class SkipAutowireArrayTypes
{
    /**
     * @var Command[]
     */
    private $commands;

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }
}

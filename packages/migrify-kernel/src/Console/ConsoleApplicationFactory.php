<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Console;

use Symfony\Component\Console\Command\Command;

final class ConsoleApplicationFactory
{
    /**
     * @var Command[]
     */
    private $commands = [];

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }

    public function create(): AutowiredConsoleApplication
    {
        return new AutowiredConsoleApplication($this->commands);
    }
}

<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Console;

use Symfony\Component\Console\Command\Command;

final class AutowiredConsoleApplication extends AbstractSymplifyConsoleApplication
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands, string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($commands, $name, $version);
    }
}

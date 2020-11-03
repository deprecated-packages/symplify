<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Console;

use Symfony\Component\Console\Command\Command;
use Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication;

final class AutodiscoveryApplication extends AbstractSymplifyConsoleApplication
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);

        parent::__construct();
    }
}

<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class EasyCIConsoleApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);
        parent::__construct('Easy CI');
    }
}

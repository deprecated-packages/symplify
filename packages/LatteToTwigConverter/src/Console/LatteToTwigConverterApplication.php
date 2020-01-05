<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class LatteToTwigConverterApplication extends Application
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

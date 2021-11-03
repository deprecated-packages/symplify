<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class ConfigTransfomerConsoleApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        parent::__construct('Config Transformer');
        $this->addCommands($commands);
    }
}

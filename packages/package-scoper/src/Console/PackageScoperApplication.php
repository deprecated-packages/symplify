<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Console;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Command\Command;
use Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication;
use Throwable;

final class PackageScoperApplication extends AbstractSymplifyConsoleApplication
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);

        parent::__construct('Package Scoper', $this->getPrettyVersion());
    }

    private function getPrettyVersion(): string
    {
        try {
            $version = PrettyVersions::getVersion('symplify/package-scoper');
            return $version->getPrettyVersion();
        } catch (Throwable $throwable) {
            return 'Unknown';
        }
    }
}

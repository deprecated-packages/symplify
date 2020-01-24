<?php

declare(strict_types=1);

namespace Symplify\Statie\Migrator\Command\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrateReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function reportPathOperation(string $action, string $oldPath, ?string $newPath = null): void
    {
        $directoryOrFile = is_dir($oldPath) ? 'directory' : 'file';

        $message = sprintf('%s %s "%s"', $action, $directoryOrFile, $oldPath);
        if ($newPath !== null) {
            $message .= sprintf(' to "%s"', $newPath);
        }

        $this->symfonyStyle->note($message);
    }
}

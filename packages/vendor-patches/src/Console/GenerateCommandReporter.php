<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Console;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\VendorPatches\ValueObject\OldAndNewFileInfo;

final class GenerateCommandReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    public function reportIdenticalNewAndOldFile(OldAndNewFileInfo $oldAndNewFileInfo): void
    {
        $message = sprintf(
            'Files "%s" and "%s" have the same content. Did you forgot to change it?',
            $oldAndNewFileInfo->getOldFileRelativePath(),
            $oldAndNewFileInfo->getNewFileRelativePath()
        );

        $this->symfonyStyle->warning($message);
    }
}

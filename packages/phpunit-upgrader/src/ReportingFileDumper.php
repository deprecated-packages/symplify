<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ReportingFileDumper
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private ConsoleDiffer $consoleDiffer,
        private SmartFileSystem $smartFileSystem
    ) {
    }

    public function processChangedFileInfo(SmartFileInfo $testFileInfo, string $changedContent): void
    {
        $this->symfonyStyle->newLine();
        $this->consoleDiffer->diff($testFileInfo->getContents(), $changedContent);

        // update file content
        $this->smartFileSystem->dumpFile($testFileInfo->getPathname(), $changedContent);
        $message = sprintf('File "%s" was updated', $testFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);
    }
}

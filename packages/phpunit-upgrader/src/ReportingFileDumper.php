<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ReportingFileDumper
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ConsoleDiffer
     */
    private $consoleDiffer;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ConsoleDiffer $consoleDiffer,
        SmartFileSystem $smartFileSystem
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->consoleDiffer = $consoleDiffer;
        $this->smartFileSystem = $smartFileSystem;
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

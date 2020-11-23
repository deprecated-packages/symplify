<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\PHPUnitUpgrader\FileInfoDecorator\AssertContainsInfoDecorator;
use Symplify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class AssertContainsMethodCallRenamer
{
    /**
     * @var AssertContainsInfoDecorator
     */
    private $assertContainsInfoDecorator;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var ConsoleDiffer
     */
    private $consoleDiffer;

    public function __construct(
        AssertContainsInfoDecorator $assertContainsInfoDecorator,
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        ConsoleDiffer $consoleDiffer
    ) {
        $this->assertContainsInfoDecorator = $assertContainsInfoDecorator;
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->consoleDiffer = $consoleDiffer;
    }

    public function renameFileInfos(array $fileInfos, SmartFileInfo $errorReportFileInfo): void
    {
        foreach ($fileInfos as $fileInfo) {
            $filePathWithContent = new FilePathWithContent(
                $fileInfo->getRelativeFilePathFromCwd(),
                $fileInfo->getContents()
            );

            $changedContent = $this->assertContainsInfoDecorator->decorate($filePathWithContent, $errorReportFileInfo);
            if ($changedContent === $fileInfo->getContents()) {
                continue;
            }

            $this->processChangedFileInfo($fileInfo, $changedContent);
        }
    }

    private function processChangedFileInfo(SmartFileInfo $testFileInfo, string $changedContent): void
    {
        $this->symfonyStyle->newLine();
        $this->consoleDiffer->diff($testFileInfo->getContents(), $changedContent);

        // update file content
        $this->smartFileSystem->dumpFile($testFileInfo->getPathname(), $changedContent);
        $message = sprintf('File "%s" was updated', $testFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);
    }
}

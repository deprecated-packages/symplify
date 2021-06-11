<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\PHPUnitUpgrader;

use Symplify\PHPUnitUpgrader\FileInfoDecorator\AssertContainsInfoDecorator;
use Symplify\PHPUnitUpgrader\ReportingFileDumper;
use Symplify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsPHPUnitUpgrader
{
    public function __construct(
        private AssertContainsInfoDecorator $assertContainsInfoDecorator,
        private ReportingFileDumper $reportingFileDumper
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
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

            $this->reportingFileDumper->processChangedFileInfo($fileInfo, $changedContent);
        }
    }
}

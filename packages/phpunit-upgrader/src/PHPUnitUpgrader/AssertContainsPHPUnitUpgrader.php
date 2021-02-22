<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\PHPUnitUpgrader;

use Symplify\PHPUnitUpgrader\FileInfoDecorator\AssertContainsInfoDecorator;
use Symplify\PHPUnitUpgrader\ReportingFileDumper;
use Symplify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsPHPUnitUpgrader
{
    /**
     * @var AssertContainsInfoDecorator
     */
    private $assertContainsInfoDecorator;

    /**
     * @var ReportingFileDumper
     */
    private $reportingFileDumper;

    public function __construct(
        AssertContainsInfoDecorator $assertContainsInfoDecorator,
        ReportingFileDumper $reportingFileDumper
    ) {
        $this->assertContainsInfoDecorator = $assertContainsInfoDecorator;
        $this->reportingFileDumper = $reportingFileDumper;
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

            $this->reportingFileDumper->processChangedFileInfo($fileInfo, $changedContent);
        }
    }
}

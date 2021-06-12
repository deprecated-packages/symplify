<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\PHPUnitUpgrader;

use Symplify\PHPUnitUpgrader\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator;
use Symplify\PHPUnitUpgrader\ReportingFileDumper;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VoidPHPUnitUpgrader
{
    public function __construct(
        private SetUpTearDownVoidFileInfoDecorator $setUpTearDownVoidFileInfoDecorator,
        private ReportingFileDumper $reportingFileDumper
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function completeFileInfos(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $changedContent = $this->setUpTearDownVoidFileInfoDecorator->decorate($fileInfo);
            if ($changedContent === $fileInfo->getContents()) {
                continue;
            }

            $this->reportingFileDumper->processChangedFileInfo($fileInfo, $changedContent);
        }
    }
}

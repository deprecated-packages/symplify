<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\PHPUnitUpgrader;

use Symplify\PHPUnitUpgrader\FileInfoDecorator\AssertContainsInfoDecorator;
use Symplify\PHPUnitUpgrader\ValueObject\FilePathWithContent;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsPHPUnitUpgrader extends AbstractPHPUnitUpgrader
{
    /**
     * @var AssertContainsInfoDecorator
     */
    private $assertContainsInfoDecorator;

    public function __construct(AssertContainsInfoDecorator $assertContainsInfoDecorator)
    {
        $this->assertContainsInfoDecorator = $assertContainsInfoDecorator;
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
}

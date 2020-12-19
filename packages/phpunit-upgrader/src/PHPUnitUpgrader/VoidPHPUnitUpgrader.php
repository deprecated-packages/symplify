<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\PHPUnitUpgrader;

use Symplify\PHPUnitUpgrader\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator;

final class VoidPHPUnitUpgrader extends AbstractPHPUnitUpgrader
{
    /**
     * @var SetUpTearDownVoidFileInfoDecorator
     */
    private $setUpTearDownVoidFileInfoDecorator;

    public function __construct(SetUpTearDownVoidFileInfoDecorator $setUpTearDownVoidFileInfoDecorator)
    {
        $this->setUpTearDownVoidFileInfoDecorator = $setUpTearDownVoidFileInfoDecorator;
    }

    public function completeFileInfos(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            $changedContent = $this->setUpTearDownVoidFileInfoDecorator->decorate($fileInfo);
            if ($changedContent === $fileInfo->getContents()) {
                continue;
            }

            $this->processChangedFileInfo($fileInfo, $changedContent);
        }
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\PHPUnitUpgrader\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class VoidCompleter
{
    /**
     * @var SetUpTearDownVoidFileInfoDecorator
     */
    private $setUpTearDownVoidFileInfoDecorator;

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
        SetUpTearDownVoidFileInfoDecorator $setUpTearDownVoidFileInfoDecorator,
        SymfonyStyle $symfonyStyle,
        ConsoleDiffer $consoleDiffer,
        SmartFileSystem $smartFileSystem
    ) {
        $this->setUpTearDownVoidFileInfoDecorator = $setUpTearDownVoidFileInfoDecorator;
        $this->symfonyStyle = $symfonyStyle;
        $this->consoleDiffer = $consoleDiffer;
        $this->smartFileSystem = $smartFileSystem;
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

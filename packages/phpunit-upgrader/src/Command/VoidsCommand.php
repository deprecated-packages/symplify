<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PHPUnitUpgrader\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator;
use Symplify\PHPUnitUpgrader\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VoidsCommand extends AbstractSymplifyCommand
{
    /**
     * @var SetUpTearDownVoidFileInfoDecorator
     */
    private $setUpTearDownVoidFileInfoDecorator;

    /**
     * @var ConsoleDiffer
     */
    private $consoleDiffer;

    public function __construct(
        SetUpTearDownVoidFileInfoDecorator $assertContainsInfoDecorator,
        ConsoleDiffer $consoleDiffer
    ) {
        $this->setUpTearDownVoidFileInfoDecorator = $assertContainsInfoDecorator;
        $this->consoleDiffer = $consoleDiffer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Add `void` to `setUp()` and `tearDown()` methods');
        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED, 'Path to tests directory');
        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Do no change, only show the diff');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(Option::SOURCE);
        $this->fileSystemGuard->ensureDirectoryExists($source);

        $testFileInfos = $this->smartFinder->find([$source], '#Test\.php#');

        foreach ($testFileInfos as $testFileInfo) {
            $changedContent = $this->setUpTearDownVoidFileInfoDecorator->decorate($testFileInfo);
            if ($changedContent === $testFileInfo->getContents()) {
                continue;
            }

            $this->processChangedFileInfo($testFileInfo, $changedContent);
        }

        $this->symfonyStyle->success('void is at in all setUp()/tearDown() methods now');

        return ShellCode::SUCCESS;
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

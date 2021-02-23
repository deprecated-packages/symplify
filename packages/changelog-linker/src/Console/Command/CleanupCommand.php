<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\ChangelogCleaner;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class CleanupCommand extends AbstractSymplifyCommand
{
    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    /**
     * @var ChangelogCleaner
     */
    private $changelogCleaner;

    public function __construct(ChangelogFileSystem $changelogFileSystem, ChangelogCleaner $changelogCleaner)
    {
        parent::__construct();

        $this->changelogFileSystem = $changelogFileSystem;
        $this->changelogCleaner = $changelogCleaner;
    }

    protected function configure(): void
    {
        $this->setDescription('Removes dead links from CHANGELOG.md');
        $this->addOption(Option::CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Config file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $changelogContent = $this->changelogFileSystem->readChangelog();

        $processedChangelogContent = $this->changelogCleaner->processContent($changelogContent);
        $this->changelogFileSystem->storeChangelog($processedChangelogContent);

        $this->symfonyStyle->success('Changelog is now clean from duplicates!');

        return ShellCode::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\ChangelogCleaner;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\PackageBuilder\Console\ShellCode;

final class CleanupCommand extends Command
{
    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    /**
     * @var ChangelogCleaner
     */
    private $changelogCleaner;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        ChangelogFileSystem $changelogFileSystem,
        ChangelogCleaner $changelogCleaner,
        SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();

        $this->changelogFileSystem = $changelogFileSystem;
        $this->changelogCleaner = $changelogCleaner;
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setDescription('Removes dead links from CHANGELOG.md');
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

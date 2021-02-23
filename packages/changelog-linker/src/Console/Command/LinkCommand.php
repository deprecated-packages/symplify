<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class LinkCommand extends AbstractSymplifyCommand
{
    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    public function __construct(ChangelogLinker $changelogLinker, ChangelogFileSystem $changelogFileSystem)
    {
        parent::__construct();

        $this->changelogLinker = $changelogLinker;
        $this->changelogFileSystem = $changelogFileSystem;
    }

    protected function configure(): void
    {
        $this->setDescription('Adds links to CHANGELOG.md');
        $this->addOption(Option::CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Config file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $changelogContent = $this->changelogFileSystem->readChangelog();

        $processedChangelogContent = $this->changelogLinker->processContentWithLinkAppends($changelogContent);

        $this->changelogFileSystem->storeChangelog($processedChangelogContent);

        $this->symfonyStyle->success('Changelog PRs, links, users and versions are now linked!');

        return ShellCode::SUCCESS;
    }
}

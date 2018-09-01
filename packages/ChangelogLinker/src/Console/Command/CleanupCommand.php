<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\ChangelogCleaner;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

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

    public function __construct(ChangelogFileSystem $changelogFileSystem, ChangelogCleaner $changelogCleaner)
    {
        parent::__construct();
        $this->changelogFileSystem = $changelogFileSystem;
        $this->changelogCleaner = $changelogCleaner;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Removes dead links from CHANGELOG.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $changelogContent = $this->changelogFileSystem->readChangelog();

        $processedChangelogContent = $this->changelogCleaner->processContent($changelogContent);

        $this->changelogFileSystem->storeChangelog($processedChangelogContent);

        // success
        return 0;
    }
}

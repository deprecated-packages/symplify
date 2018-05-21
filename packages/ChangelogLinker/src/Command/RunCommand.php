<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Exception\FileNotFoundException;

final class RunCommand extends Command
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    public function __construct(ChangelogApplication $changelogApplication)
    {
        $this->changelogApplication = $changelogApplication;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('run');
        $this->addArgument('changelog-file', InputArgument::OPTIONAL, 'CHANGELOG.md file', 'CHANGELOG.md');
        $this->addOption(
            'repository',
            'r',
            InputOption::VALUE_REQUIRED,
            'Add Github repository url, e.g. "https://github.com/Symplify/Symplify"',
            'https://github.com/Symplify/Symplify'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $changelogFile = $input->getArgument('changelog-file');
        if (! file_exists($changelogFile)) {
            throw new FileNotFoundException(sprintf('Changelog file "%s" was not found' . PHP_EOL, $changelogFile));
        }

        $processedChangelogFile = $this->changelogApplication->processFile($changelogFile);

        // save
        file_put_contents($changelogFile, $processedChangelogFile);

        // success
        return 0;
    }
}

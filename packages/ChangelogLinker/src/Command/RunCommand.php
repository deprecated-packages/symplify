<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Exception\FileNotFoundException;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class RunCommand extends Command
{
    /**
     * @var string
     */
    private const REPOSITORY_OPTION = 'repository';

    /**
     * @var string
     */
    private const CHANGELOG_FILE_OPTION = 'changelog-file';

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
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(self::CHANGELOG_FILE_OPTION, InputArgument::OPTIONAL, 'CHANGELOG.md file', 'CHANGELOG.md');
        $this->addOption(
            self::REPOSITORY_OPTION,
            'r',
            InputOption::VALUE_REQUIRED,
            'Add Github repository url, e.g. "https://github.com/Symplify/Symplify"',
            'https://github.com/Symplify/Symplify'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $changelogFile = $input->getArgument(self::CHANGELOG_FILE_OPTION);
        if (! file_exists($changelogFile)) {
            throw new FileNotFoundException(sprintf('Changelog file "%s" was not found' . PHP_EOL, $changelogFile));
        }

        $repositoryUrl = $input->getOption(self::REPOSITORY_OPTION);

        $processedChangelogFile = $this->changelogApplication->processFile($changelogFile, $repositoryUrl);

        // save
        file_put_contents($changelogFile, $processedChangelogFile);

        // success
        return 0;
    }
}

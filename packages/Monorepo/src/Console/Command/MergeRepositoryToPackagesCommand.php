<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Monorepo\Exception\MissingConfigurationException;
use Symplify\Monorepo\Filesystem\Filesystem;
use Symplify\Monorepo\Worker\MoveHistoryWorker;
use Symplify\Monorepo\Worker\RepositoryWorker;

final class MergeRepositoryToPackagesCommand extends Command
{
    /**
     * @var string
     */
    private const DIRECTORY_OPTION_NAME = 'directory';

    /**
     * @var string
     */
    private const REPOSITORY_ARGUMENT_NAME = 'git-repository';

    /**
     * @var RepositoryWorker
     */
    private $fetchRepositoryWorker;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var MoveHistoryWorker
     */
    private $moveHistoryWorker;

    public function __construct(
        RepositoryWorker $fetchRepositoryWorker,
        Filesystem $filesystem,
        MoveHistoryWorker $moveHistoryWorker
    ) {
        $this->fetchRepositoryWorker = $fetchRepositoryWorker;
        $this->filesystem = $filesystem;
        $this->moveHistoryWorker = $moveHistoryWorker;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('merge-repository-to-package');
        $this->addArgument(self::REPOSITORY_ARGUMENT_NAME, InputArgument::REQUIRED, 'Path to .git repository');
        $this->addOption(
            self::DIRECTORY_OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Local directory'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensurePackageDirectoryIsSet($input);

        // run.sh - DONE
        $repository = $input->getArgument(self::REPOSITORY_ARGUMENT_NAME);

        $this->fetchRepositoryWorker->fetchAndMergeRepository($repository);

        // run2.sh
        $cwd = getcwd();
        $newPackageDirectory = $input->getOption(self::DIRECTORY_OPTION_NAME);

        $finder = $finder = $this->filesystem->findMergedPackageFiles($cwd);
        $this->filesystem->copyFinderFilesToDirectory($finder, $newPackageDirectory);
        $this->moveHistoryWorker->prependHistoryToNewPackageFiles($finder, $newPackageDirectory);
        $this->filesystem->clearEmptyDirectories($cwd);
    }

    private function ensurePackageDirectoryIsSet(InputInterface $input): void
    {
        $directory = $input->getOption(self::DIRECTORY_OPTION_NAME);
        if ($directory) {
            return;
        }

        throw new MissingConfigurationException(sprintf(
            'Configuration option "--%s" is missing',self::DIRECTORY_OPTION_NAME
        ));
    }
}

<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Monorepo\Filesystem\Filesystem;
use Symplify\Monorepo\Worker\MoveHistoryWorker;
use Symplify\Monorepo\Worker\RepositoryWorker;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class BuildCommand extends Command
{
    /**
     * @var string
     */
    private const OUTPUT_DIRECTORY = 'git-repository';

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
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(
        RepositoryWorker $fetchRepositoryWorker,
        Filesystem $filesystem,
        MoveHistoryWorker $moveHistoryWorker,
        ParameterProvider $parameterProvider
    ) {
        $this->fetchRepositoryWorker = $fetchRepositoryWorker;
        $this->filesystem = $filesystem;
        $this->moveHistoryWorker = $moveHistoryWorker;
        $this->parameterProvider = $parameterProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('build');
        $this->getDescription('Creates monolitic repository from provided config.');
        $this->addArgument(self::OUTPUT_DIRECTORY, InputArgument::REQUIRED, 'Path to empty .git repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // run.sh - DONE
        $repository = $input->getArgument(self::OUTPUT_DIRECTORY);

        dump($repository);
        die;

        $this->fetchRepositoryWorker->fetchAndMergeRepository($repository);

        // run2.sh
        $cwd = getcwd();
        // read from config
        $newPackageDirectory = //..;

        $finder = $finder = $this->filesystem->findMergedPackageFiles($cwd);
        $this->filesystem->copyFinderFilesToDirectory($finder, $newPackageDirectory);
        $this->moveHistoryWorker->prependHistoryToNewPackageFiles($finder, $newPackageDirectory);
        $this->filesystem->clearEmptyDirectories($cwd);
    }
}

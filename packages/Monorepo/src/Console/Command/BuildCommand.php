<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Monorepo\Exception\MissingConfigurationSectionException;
use Symplify\Monorepo\Filesystem\Filesystem;
use Symplify\Monorepo\Worker\MoveHistoryWorker;
use Symplify\Monorepo\Worker\RepositoryWorker;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class BuildCommand extends Command
{
    /**
     * @var string
     */
    private const MONOREPO_DIRECTORY = 'monorepo-directory';

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
        $this->setDescription('Creates monolitic repository from provided config.');
        $this->addArgument(self::MONOREPO_DIRECTORY, InputArgument::REQUIRED, 'Path to empty .git repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $build = $this->parameterProvider->provideParameter('build');
        $this->ensureConfigSectionIsFilled($build, 'build');

        $monorepoDirectory = $input->getArgument(self::MONOREPO_DIRECTORY);
        foreach ($build as $repositoryUrl => $packagesSubdirectory) {
            $this->mergeRepositoryToMonorepoDirectory($repositoryUrl, $monorepoDirectory, $packagesSubdirectory);
        }
    }

    /**
     * @param mixed $config
     */
    private function ensureConfigSectionIsFilled($config, string $section): void
    {
        if ($config) {
            return;
        }

        throw new MissingConfigurationSectionException(sprintf(
            'Section "%s" in config is rqeuired. Complete it to "%s" file under "parameters"',
            $section,
            'monorepo.yml'
        ));
    }

    private function mergeRepositoryToMonorepoDirectory(
        string $repositoryUrl,
        string $monorepoDirectory,
        string $packageSubdirectory
    ): void {
        $this->fetchRepositoryWorker->fetchAndMergeRepository($repositoryUrl, $monorepoDirectory);

        dump($monorepoDirectory);
        die;

        // run2.sh
        $cwd = $monorepoDirectory;
        // read from config
        $newPackageDirectory = //..;

        // @todo: use git status maybe? it's better way to find those files
        $finder = $finder = $this->filesystem->findMergedPackageFiles($cwd);
        $this->filesystem->copyFinderFilesToDirectory($finder, $packageSubdirectory);
        $this->moveHistoryWorker->prependHistoryToNewPackageFiles($finder, $packageSubdirectory);
        $this->filesystem->clearEmptyDirectories($cwd);
    }
}

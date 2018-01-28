<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use PhpParser\Node\Param;
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
        $outputDirectory = $input->getArgument(self::OUTPUT_DIRECTORY);

        $build = $this->parameterProvider->provideParameter('build');
        $this->ensureConfigSectionIsFilled($build, 'build');

        foreach ($build as $repositoryUrl => $monorepoDirectory) {
            $this->mergeRepositoryToMonorepoDiretory($repositoryUrl, $outputDirectory, $monorepoDirectory);
        }
    }

    private function ensureConfigSectionIsFilled($config = null, string $section): void
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

    private function mergeRepositoryToMonorepoDiretory(string $repositoryUrl, string $monorepoDirectory, string $packageDirectory): void
    {
        //

        $this->fetchRepositoryWorker->fetchAndMergeRepository($repositoryUrl);

        // run2.sh
        $cwd = $monorepoDirectory;
        // read from config
        $newPackageDirectory = //..;

        $finder = $finder = $this->filesystem->findMergedPackageFiles($cwd);
        $this->filesystem->copyFinderFilesToDirectory($finder, $packageDirectory);
        $this->moveHistoryWorker->prependHistoryToNewPackageFiles($finder, $packageDirectory);
        $this->filesystem->clearEmptyDirectories($cwd);
    }
}

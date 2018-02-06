<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Monorepo\Configuration\ConfigurationGuard;
use Symplify\Monorepo\Configuration\ConfigurationOptions;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Symplify\Monorepo\Filesystem\Filesystem;
use Symplify\Monorepo\PackageToRepositorySplitter;
use Symplify\Monorepo\Process\ProcessFactory;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class SplitCommand extends Command
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var ConfigurationGuard
     */
    private $configurationGuard;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var PackageToRepositorySplitter
     */
    private $packageToRepositorySplitter;

    /**
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    public function __construct(
        ParameterProvider $parameterProvider,
        ConfigurationGuard $configurationGuard,
        SymfonyStyle $symfonyStyle,
        Filesystem $filesystem,
        PackageToRepositorySplitter $packageToRepositorySplitter,
        ProcessFactory $processFactory,
        RepositoryGuard $repositoryGuard
    ) {
        $this->parameterProvider = $parameterProvider;
        $this->configurationGuard = $configurationGuard;
        $this->symfonyStyle = $symfonyStyle;
        $this->filesystem = $filesystem;
        $this->packageToRepositorySplitter = $packageToRepositorySplitter;
        $this->processFactory = $processFactory;
        $this->repositoryGuard = $repositoryGuard;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Split monolithic repository from provided config to many repositories.');
        $this->addArgument(
            ConfigurationOptions::MONOREPO_DIRECTORY_ARGUMENT,
            InputArgument::OPTIONAL,
            'Path to .git repository',
            getcwd()
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $splitConfig = $this->parameterProvider->provideParameter('split');
        $this->configurationGuard->ensureConfigSectionIsFilled($splitConfig, 'split');

        $monorepoDirectory = $input->getArgument(ConfigurationOptions::MONOREPO_DIRECTORY_ARGUMENT);
        $this->repositoryGuard->ensureIsRepositoryDirectory($monorepoDirectory);
        $this->processFactory->setCurrentWorkingDirectory($monorepoDirectory);

        $subsplitDirectory = $this->getSubsplitDirectory($monorepoDirectory);

        $process = $this->processFactory->createSubsplitInit();
        $process->run();

        $this->symfonyStyle->success(sprintf('Directory "%s" with local clone created', $subsplitDirectory));

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories($splitConfig, $monorepoDirectory);

        $this->filesystem->deleteDirectory($subsplitDirectory);
        $this->symfonyStyle->success(sprintf('Directory "%s" cleaned', $subsplitDirectory));
    }

    /**
     * Cache directory that is required by "git subsplit" command.
     */
    private function getSubsplitDirectory(string $cwd): string
    {
        return $cwd . '/.subsplit';
    }
}

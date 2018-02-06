<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use GitWrapper\GitWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Monorepo\Configuration\ConfigurationGuard;
use Symplify\Monorepo\Configuration\ConfigurationOptions;
use Symplify\Monorepo\Exception\Filesystem\DirectoryNotFoundException;
use Symplify\Monorepo\Exception\Git\InvalidGitRepositoryException;
use Symplify\Monorepo\Filesystem\Filesystem;
use Symplify\Monorepo\PackageToRepositorySplitter;
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
     * @var GitWrapper
     */
    private $gitWrapper;

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

    public function __construct(
        ParameterProvider $parameterProvider,
        ConfigurationGuard $configurationGuard,
        GitWrapper $gitWrapper,
        SymfonyStyle $symfonyStyle,
        Filesystem $filesystem,
        PackageToRepositorySplitter $packageToRepositorySplitter
    ) {
        $this->parameterProvider = $parameterProvider;
        $this->configurationGuard = $configurationGuard;
        $this->gitWrapper = $gitWrapper;
        $this->symfonyStyle = $symfonyStyle;
        $this->filesystem = $filesystem;
        $this->packageToRepositorySplitter = $packageToRepositorySplitter;

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

        // git subsplit init .git
        $monorepoDirectory = $input->getArgument(ConfigurationOptions::MONOREPO_DIRECTORY_ARGUMENT);
        $this->ensureIsGitRepository($monorepoDirectory);

        $subsplitDirectory = $monorepoDirectory . '/.subsplit';
        $gitWorkingCopy = $this->gitWrapper->workingCopy($monorepoDirectory);

        // @todo check exception if subsplit alias not installed
        $gitWorkingCopy->run('subsplit', ['init', $monorepoDirectory . '/.git']);
        $this->symfonyStyle->success(sprintf('Directory "%s" with local clone created', $subsplitDirectory));

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories($splitConfig);

        $this->filesystem->deleteDirectory($subsplitDirectory);
        $this->symfonyStyle->success(sprintf('Directory "%s" cleaned', $subsplitDirectory));
    }

    private function ensureIsGitRepository(string $repository): void
    {
        if (! file_exists($repository)) {
            throw new DirectoryNotFoundException(sprintf('Directory for repository "%s" was not found', $repository));
        }

        if (! file_exists($repository . '/.git')) {
            throw new InvalidGitRepositoryException(sprintf('.git was not found in "%s" directory', $repository));
        }
    }
}

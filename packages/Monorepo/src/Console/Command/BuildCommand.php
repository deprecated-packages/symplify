<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Monorepo\Configuration\ConfigurationGuard;
use Symplify\Monorepo\Exception\MissingConfigurationSectionException;
use Symplify\Monorepo\RepositoryToPackageMerger;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class BuildCommand extends Command
{
    /**
     * @var string
     */
    private const MONOREPO_DIRECTORY = 'monorepo-directory';

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var RepositoryToPackageMerger
     */
    private $repositoryToPackageMerger;
    /**
     * @var ConfigurationGuard
     */
    private $configurationGuard;

    public function __construct(
        ParameterProvider $parameterProvider,
        RepositoryToPackageMerger $repositoryToPackageMerger,
        ConfigurationGuard $configurationGuard
    ) {
        $this->parameterProvider = $parameterProvider;
        $this->repositoryToPackageMerger = $repositoryToPackageMerger;
        $this->configurationGuard = $configurationGuard;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Creates monolitic repository from provided config.');
        $this->addArgument(self::MONOREPO_DIRECTORY, InputArgument::REQUIRED, 'Path to empty .git repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $build = $this->parameterProvider->provideParameter('build');
        $this->configurationGuard->ensureConfigSectionIsFilled($build, 'build');

        $monorepoDirectory = $input->getArgument(self::MONOREPO_DIRECTORY);
        foreach ($build as $repositoryUrl => $packagesSubdirectory) {
            $this->repositoryToPackageMerger->mergeRepositoryToPackage(
                $repositoryUrl,
                $monorepoDirectory,
                $packagesSubdirectory
            );
        }
    }
}

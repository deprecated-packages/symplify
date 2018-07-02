<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\PackageToRepositorySplitter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class SplitCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    /**
     * @var string[]
     */
    private $directoriesToRepositories = [];

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var PackageToRepositorySplitter
     */
    private $packageToRepositorySplitter;

    /**
     * @param string[] $directoriesToRepositories
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        RepositoryGuard $repositoryGuard,
        array $directoriesToRepositories,
        string $rootDirectory,
        PackageToRepositorySplitter $packageToRepositorySplitter
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->repositoryGuard = $repositoryGuard;
        $this->directoriesToRepositories = $directoriesToRepositories;
        $this->rootDirectory = $rootDirectory;
        $this->packageToRepositorySplitter = $packageToRepositorySplitter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->repositoryGuard->ensureIsRepositoryDirectory($this->rootDirectory);

        $isVerbose = $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories(
            $this->directoriesToRepositories,
            $this->rootDirectory,
            $this->getSubsplitDirectory(),
            $isVerbose
        );

        // success
        return 0;
    }

    private function getSubsplitDirectory(): string
    {
        $tempDirectory = sys_get_temp_dir() . '/_subsplit';

        // clean it
        FileSystem::delete($tempDirectory);
        // convention used by split.sh script
//        return $this->rootDirectory . '/.subsplit';
        # @todo: should be in config?
        return $tempDirectory;
    }
}

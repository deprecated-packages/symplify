<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\PackageToRepositorySplitter;
use Symplify\MonorepoBuilder\Split\Process\ProcessFactory;
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
     * @var ProcessFactory
     */
    private $processFactory;

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
        ProcessFactory $processFactory,
        string $rootDirectory,
        PackageToRepositorySplitter $packageToRepositorySplitter
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->repositoryGuard = $repositoryGuard;
        $this->directoriesToRepositories = $directoriesToRepositories;
        $this->processFactory = $processFactory;
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

        $subsplitDirectory = $this->getSubsplitDirectory();

        // init subsbplit
        $process = $this->processFactory->createSubsplitInit();
        $process->run();

        $this->symfonyStyle->success(sprintf('Directory "%s" with local clone created', $subsplitDirectory));

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories(
            $this->directoriesToRepositories,
            $this->rootDirectory
        );

        FileSystem::delete($subsplitDirectory);

        $this->symfonyStyle->success(sprintf('Directory "%s" cleaned', $subsplitDirectory));

        // success
        return 0;
    }

    private function getSubsplitDirectory(): string
    {
        // convention used by split.sh script
        return $this->rootDirectory . '/.subsplit';
    }
}

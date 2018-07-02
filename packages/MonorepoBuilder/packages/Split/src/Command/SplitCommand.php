<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\PackageToRepositorySplitter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class SplitCommand extends Command
{
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
     * @var string
     */
    private $subsplitCacheDirectory;

    /**
     * @param string[] $directoriesToRepositories
     */
    public function __construct(
        RepositoryGuard $repositoryGuard,
        array $directoriesToRepositories,
        string $rootDirectory,
        PackageToRepositorySplitter $packageToRepositorySplitter,
        string $subsplitCacheDirectory
    ) {
        parent::__construct();

        $this->repositoryGuard = $repositoryGuard;
        $this->directoriesToRepositories = $directoriesToRepositories;
        $this->rootDirectory = $rootDirectory;
        $this->packageToRepositorySplitter = $packageToRepositorySplitter;
        $this->subsplitCacheDirectory = $subsplitCacheDirectory;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->repositoryGuard->ensureIsRepositoryDirectory($this->rootDirectory);

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories(
            $this->directoriesToRepositories,
            $this->rootDirectory,
            $output->isVerbose()
        );

        // success
        return 0;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Split\Configuration\Option;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\PackageToRepositorySplitter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class SplitCommand extends Command
{
    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var string[]
     */
    private $directoriesToRepositories = [];

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    /**
     * @var PackageToRepositorySplitter
     */
    private $packageToRepositorySplitter;

    /**
     * @param string[] $directoriesToRepositories
     */
    public function __construct(
        RepositoryGuard $repositoryGuard,
        array $directoriesToRepositories,
        string $rootDirectory,
        PackageToRepositorySplitter $packageToRepositorySplitter
    ) {
        parent::__construct();

        $this->repositoryGuard = $repositoryGuard;
        $this->directoriesToRepositories = $directoriesToRepositories;
        $this->rootDirectory = $rootDirectory;
        $this->packageToRepositorySplitter = $packageToRepositorySplitter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            sprintf(
                'Splits monorepo packages to standalone repositories as defined in "%s" section of "%s" config.',
                'parameters > directories_to_repositories',
                'monorepo-builder.yml'
            )
        );

        $this->addOption(
            Option::BRANCH,
            null,
            InputOption::VALUE_OPTIONAL,
            'Branch to run split on, defaults to current branch'
        );
        $this->addOption(
            Option::MAX_PROCESSES,
            null,
            InputOption::VALUE_REQUIRED,
            'Maximum number of processes to run in parallel'
        );
        $this->addOption(
            Option::TAG,
            't',
            InputOption::VALUE_REQUIRED,
            'Specify the Git tag use for split. Use the most recent one by default'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->repositoryGuard->ensureIsRepositoryDirectory($this->rootDirectory);

        $maxProcesses = $input->getOption(Option::MAX_PROCESSES) ? (int)
            $input->getOption(Option::MAX_PROCESSES)
            : null;

        /** @var string|null $tag */
        $tag = $input->getOption(Option::TAG);

        $branch = $input->getOption(Option::BRANCH) ? (string) $input->getOption(Option::BRANCH) : null;

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories(
            $this->directoriesToRepositories,
            $this->rootDirectory,
            $branch,
            $maxProcesses,
            $tag
        );

        return ShellCode::SUCCESS;
    }
}

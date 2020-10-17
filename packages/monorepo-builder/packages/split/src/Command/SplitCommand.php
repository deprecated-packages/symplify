<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Split\Configuration\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\FileSystem\DirectoryToRepositoryProvider;
use Symplify\MonorepoBuilder\Split\PackageToRepositorySplitter;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class SplitCommand extends Command
{
    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    /**
     * @var PackageToRepositorySplitter
     */
    private $packageToRepositorySplitter;

    /**
     * @var DirectoryToRepositoryProvider
     */
    private $directoryToRepositoryProvider;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        RepositoryGuard $repositoryGuard,
        ParameterProvider $parameterProvider,
        PackageToRepositorySplitter $packageToRepositorySplitter,
        DirectoryToRepositoryProvider $directoryToRepositoryProvider,
        SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();

        $this->repositoryGuard = $repositoryGuard;
        $this->packageToRepositorySplitter = $packageToRepositorySplitter;
        $this->directoryToRepositoryProvider = $directoryToRepositoryProvider;
        $this->symfonyStyle = $symfonyStyle;

        $this->rootDirectory = $parameterProvider->provideStringParameter(Option::ROOT_DIRECTORY);
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $description = sprintf(
            'Splits monorepo packages to standalone repositories as defined in "%s" section of "%s" config.',
            '$parameters->set(Option::DIRECTORIES_REPOSITORY, [...])',
            'monorepo-builder.php'
        );

        $this->setDescription($description);

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

        $resolvedDirectoriesToRepository = $this->directoryToRepositoryProvider->provide();
        if (count($resolvedDirectoriesToRepository) === 0) {
            $this->symfonyStyle->error('No packages to split');
            return ShellCode::SUCCESS;
        }

        $this->symfonyStyle->title('Splitting Following Packages');

        foreach ($resolvedDirectoriesToRepository as $directory => $gitRepository) {
            $message = sprintf('* "%s" directory to "%s" repository', $directory, $gitRepository);
            $this->symfonyStyle->writeln($message);
        }

        $this->symfonyStyle->newLine(2);

        // to give time to process split information
        sleep(2);

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories(
            $resolvedDirectoriesToRepository,
            $this->rootDirectory,
            $branch,
            $maxProcesses,
            $tag
        );

        $message = sprintf('Split of %d packages was successful', count($resolvedDirectoriesToRepository));
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}

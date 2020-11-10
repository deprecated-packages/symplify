<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\GitHubActionsWorkflow\MissingPackageInWorkflowResolver;
use Symplify\MonorepoBuilder\Package\PackageProvider;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckSplitTestWorkflowCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_SOURCE = 'source';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PackageProvider
     */
    private $packageProvider;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var MissingPackageInWorkflowResolver
     */
    private $missingPackageInWorkflowResolver;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageProvider $packageProvider,
        FileSystemGuard $fileSystemGuard,
        MissingPackageInWorkflowResolver $missingPackageInWorkflowResolver
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->packageProvider = $packageProvider;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->missingPackageInWorkflowResolver = $missingPackageInWorkflowResolver;
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::ARGUMENT_SOURCE,
            InputArgument::REQUIRED,
            'Path to Github Action workflow file with split tests'
        );
        $this->setDescription('Checkes split workflow for all the packages with tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packages = $this->packageProvider->provideWithTests();

        $workflowFileInfo = $this->resolveWorkflowFileInfo($input);

        $message = sprintf(
            'Checking %d packages in %s',
            count($packages),
            $workflowFileInfo->getRelativeFilePathFromCwd()
        );
        $this->symfonyStyle->title($message);

        $missingPackages = $this->missingPackageInWorkflowResolver->resolveInFileInfo($packages, $workflowFileInfo);

        if ($missingPackages === []) {
            $message = sprintf('All packages found!');
            $this->symfonyStyle->success($message);
            return ShellCode::SUCCESS;
        }

        foreach ($missingPackages as $missingPackage) {
            $errorMessage = sprintf('Package "%s" is missing', $missingPackage->getShortName());
            $this->symfonyStyle->error($errorMessage);
        }

        $this->symfonyStyle->newLine(2);

        return ShellCode::ERROR;
    }

    private function resolveWorkflowFileInfo(InputInterface $input): SmartFileInfo
    {
        $workflowFilePath = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        $workflowFilePath = getcwd() . DIRECTORY_SEPARATOR . $workflowFilePath;

        $this->fileSystemGuard->ensureFileExists($workflowFilePath, __METHOD__);

        return new SmartFileInfo($workflowFilePath);
    }
}

<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Package\PackageProvider;
use Symplify\MonorepoBuilder\ValueObject\Package;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
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

    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageProvider $packageProvider,
        FileSystemGuard $fileSystemGuard
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->packageProvider = $packageProvider;
        $this->fileSystemGuard = $fileSystemGuard;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));

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

        $message = sprintf('Checking %d packages with tests', count($packages));
        $this->symfonyStyle->title($message);

        $workflowFileInfo = $this->resolveWorkflowFileInfo($input);
        $missingPackages = $this->resolveMissingPackagesInSplitTests($packages, $workflowFileInfo);

        if ($missingPackages === []) {
            $message = sprintf('All packages found!');
            $this->symfonyStyle->success($message);
            return ShellCode::SUCCESS;
        }

        $errorMessage = sprintf(
            'Add missing packages to "%s" workflow file',
            $workflowFileInfo->getRelativeFilePathFromCwd()
        );
        $this->symfonyStyle->error($errorMessage);

        foreach ($missingPackages as $missingPackage) {
            $this->symfonyStyle->writeln(' - ' . $missingPackage->getShortName());
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

    /**
     * @param Package[] $packages
     * @return Package[]
     */
    private function resolveMissingPackagesInSplitTests(array $packages, SmartFileInfo $workflowFileInfo): array
    {
        $missingPackages = [];

        foreach ($packages as $package) {
            $packageNameItemPattern = '#\-\s+' . preg_quote($package->getShortDirectory(), '#') . '\b#';

            if (Strings::match($workflowFileInfo->getContents(), $packageNameItemPattern)) {
                $message = sprintf(
                    'Package "%s" was found in "%s"',
                    $package->getShortDirectory(),
                    $workflowFileInfo->getRelativeFilePathFromCwd()
                );
                $this->symfonyStyle->note($message);
                continue;
            }

            $missingPackages[] = $package;
        }

        return $missingPackages;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageScoper\FileMetrics;
use Symplify\PackageScoper\Finder\DevFilesFinder;
use Symplify\PackageScoper\ValueObject\Option;

final class CleanDevFilesCommand extends AbstractSymplifyCommand
{
    /**
     * @var DevFilesFinder
     */
    private $devFilesFinder;

    /**
     * @var FileMetrics
     */
    private $fileMetrics;

    public function __construct(DevFilesFinder $devFilesFinder, FileMetrics $fileMetrics)
    {
        $this->devFilesFinder = $devFilesFinder;

        parent::__construct();

        $this->fileMetrics = $fileMetrics;
    }

    protected function configure(): void
    {
        $this->setDescription('Clean dev files that are not needed for package release');
        $this->addArgument(
            Option::PATH,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory to clean'
        );

        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Dry run = no changes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (array) $input->getArgument(Option::PATH);

        $allFileInfos = $this->smartFinder->find($source, '*');
        $allFileSize = $this->fileMetrics->getFileSizeInKiloBites($allFileInfos);

        $devFileInfos = $this->devFilesFinder->findDevFilesPaths($source);
        $devFilePaths = $this->smartFileSystem->resolveFilePathsFromFileInfos($devFileInfos);

        $this->symfonyStyle->title('Removing dev files');
        $this->symfonyStyle->listing($devFilePaths);

        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);
        if ($isDryRun) {
            $messsage = sprintf('%d files would be removed [dry-run]', count($devFilePaths));
            $this->symfonyStyle->note($messsage);
        } else {
            $this->smartFileSystem->remove($devFilePaths);
            $message = sprintf('%d files were removed', count($devFilePaths));
            $this->symfonyStyle->success($message);
        }

        $devFileSize = $this->fileMetrics->getFileSizeInKiloBites($devFileInfos);

        $message = sprintf('Dev file size: %d.2 Kb', $devFileSize);
        $this->symfonyStyle->note($message);

        $message = sprintf('All file size: %d.2 Kb', $allFileSize);
        $this->symfonyStyle->note($message);

        $message = sprintf('The cleanup saves %d.1 %%', $devFileSize / $allFileSize * 100);
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}

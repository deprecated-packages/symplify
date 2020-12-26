<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageScoper\Finder\DevFilesFinder;
use Symplify\PackageScoper\ValueObject\Option;

final class CleanDevFilesCommand extends AbstractSymplifyCommand
{
    /**
     * @var DevFilesFinder
     */
    private $devFilesFinder;

    public function __construct(DevFilesFinder $devFilesFinder)
    {
        $this->devFilesFinder = $devFilesFinder;

        parent::__construct();
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

        $filePaths = $this->devFilesFinder->findDevFilesPaths($source);

        $this->symfonyStyle->title('Removing dev files');
        $this->symfonyStyle->listing($filePaths);

        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);
        if ($isDryRun) {
            $messsage = sprintf('%d files would be removed [dry-run]', count($filePaths));
            $this->symfonyStyle->note($messsage);
        } else {
            $this->smartFileSystem->remove($filePaths);
            $message = sprintf('%d files were removed', count($filePaths));
            $this->symfonyStyle->success($message);
        }

        return ShellCode::SUCCESS;
    }
}

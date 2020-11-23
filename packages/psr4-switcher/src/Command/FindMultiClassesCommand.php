<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Command;

use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Psr4Switcher\Finder\MultipleClassInOneFileFinder;

final class FindMultiClassesCommand extends AbstractSymplifyCommand
{
    /**
     * @var MultipleClassInOneFileFinder
     */
    private $multipleClassInOneFileFinder;

    public function __construct(MultipleClassInOneFileFinder $multipleClassInOneFileFinder)
    {
        $this->multipleClassInOneFileFinder = $multipleClassInOneFileFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Find multiple classes in one file');
        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to source to analyse'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = $input->getArgument(MigrifyOption::SOURCES);

        $multipleClassesByFile = $this->multipleClassInOneFileFinder->findInDirectories($source);
        if ($multipleClassesByFile === []) {
            $this->symfonyStyle->success('No files with 2+ classes found');

            return ShellCode::SUCCESS;
        }

        foreach ($multipleClassesByFile as $file => $classes) {
            $message = sprintf('File "%s" has %d classes', $file, count($classes));
            $this->symfonyStyle->section($message);
            $this->symfonyStyle->listing($classes);
        }

        return ShellCode::ERROR;
    }
}

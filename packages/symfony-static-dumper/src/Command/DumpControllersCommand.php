<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;

final class DumpControllersCommand extends Command
{
    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SymfonyStaticDumperApplication
     */
    private $symfonyStaticDumperApplication;

    public function __construct(
        SymfonyStaticDumperApplication $symfonyStaticDumperApplication,
        SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();

        $this->outputDirectory = getcwd() . '/output';

        $this->symfonyStyle = $symfonyStyle;
        $this->symfonyStaticDumperApplication = $symfonyStaticDumperApplication;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Dump controllers to the output directory');
        $this->addOption(
            'route',
            '',
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'dump only given route names, if not provided all routes are dumped'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->section('Dumping Controllers');
        $this->symfonyStaticDumperApplication->dumpControllers($this->outputDirectory, $input->getOption('route'));

        return ShellCode::SUCCESS;
    }
}

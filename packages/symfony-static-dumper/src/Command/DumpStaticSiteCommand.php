<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;
use Symplify\SymfonyStaticDumper\Enum\Option;

final class DumpStaticSiteCommand extends AbstractSymplifyCommand
{
    private string $projectDir;

    public function __construct(
        private SymfonyStaticDumperApplication $symfonyStaticDumperApplication,
        ParameterBagInterface $parameterBag
    ) {
        $this->projectDir = (string) $parameterBag->get('kernel.project_dir');

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('dump-static-site');
        $this->setDescription('Dump website to static HTML and CSS in the output directory');

        // Adding arguments options for the main command
        $this->addOption(
            Option::PUBLIC_DIRECTORY,
            null,
            InputOption::VALUE_REQUIRED,
            'Define the input public directory',
            $this->projectDir . '/public'
        );

        $this->addOption(
            Option::OUTPUT_DIRECTORY,
            null,
            InputOption::VALUE_REQUIRED,
            'Define the output directory for generated static content',
            getcwd() . '/output'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->section('Dumping static website');

        $publicDirectory = $input->getOption(Option::PUBLIC_DIRECTORY);
        $outputDirectory = $input->getOption(Option::OUTPUT_DIRECTORY);
        $this->symfonyStaticDumperApplication->run($publicDirectory, $outputDirectory);

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

        return self::SUCCESS;
    }
}

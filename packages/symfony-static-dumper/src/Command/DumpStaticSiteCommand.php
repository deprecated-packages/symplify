<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;

final class DumpStaticSiteCommand extends AbstractSymplifyCommand
{
    private string $publicDirectory;

    private string $outputDirectory;

    public function __construct(
        private SymfonyStaticDumperApplication $symfonyStaticDumperApplication,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('dump-static-site');
        $this->setDescription('Dump website to static HTML and CSS in the output directory');

        // Adding arguments options for the main command
        $this->addOption('public-dir', 'p', InputOption::VALUE_REQUIRED, 'Define the input public directory relative to the root project directory', './public');
        $this->addOption('output-dir', 'o', InputOption::VALUE_REQUIRED, 'Define the output directory relative to the execution of the comand', './output');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publicDirectory = $this->getPublicDirectory($input->getOption('public-dir'));
        $outputDirectory = $this->getOutputDirectory($input->getOption('output-dir'));
        
        $this->symfonyStyle->section('Dumping static website');
        $this->symfonyStaticDumperApplication->run($publicDirectory, $outputDirectory);

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

        return self::SUCCESS;
    }

    protected function getPublicDirectory($inputDir): string
    {
        $projectDir = (string) $this->parameterBag->get('kernel.project_dir');

            return $inputDir ? $projectDir . $inputDir : $projectDir . '/public';
    }

    protected function getOutputDirectory($outputDir): string
    {
        return getcwd() . ( $outputDir ? $outputDir : '/output');
    }
}

<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\SymfonyStaticDumper\Application\SymfonyStaticDumperApplication;

final class DumpStaticSiteCommand extends AbstractSymplifyCommand
{
    private string $publicDirectory;

    private string $outputDirectory;

    public function __construct(
        private SymfonyStaticDumperApplication $symfonyStaticDumperApplication
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('dump-static-site');
        $this->setDescription('Dump website to static HTML and CSS in the output directory');

        // Adding arguments options for the main command
        $this->addOption('public-dir', 'p', InputOption::VALUE_REQUIRED, 'Define the input public directory absolute to the root of the project', './public');
        $this->addOption('output-dir', 'o', InputOption::VALUE_REQUIRED, 'Define the output directory absolute to the execution of the comand', './output');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {   
        $this->publicDirectory = $this->parseInputPath($input->getOption('public-dir'));
        $this->outputDirectory = $this->parseInputPath($input->getOption('output-dir'));

        $this->symfonyStyle->section('Dumping static website');
        $this->symfonyStaticDumperApplication->run($this->publicDirectory, $this->outputDirectory);

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

        return self::SUCCESS;
    }

    protected function parseInputPath($path): string
    {
        $output = '';
        // check if path is absolute will false if relative
        if (strpos($path, '/') === 0) {
            $output = $path;
        }
        // check if path is relative 
        if (strpos($path, '.') === 0) {
            $output = getcwd() . ltrim($path, '.');
        }
        // check if path is neither will default to relative
        if (strlen($output) == 0) {
            $output = getcwd() . '/' . $path;
        }
        return  $output;
    }
}



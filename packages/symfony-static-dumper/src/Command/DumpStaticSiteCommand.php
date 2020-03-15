<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\RouterInterface;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SymfonyStaticDumper\Controller\ControllerDumper;
use Symplify\SymfonyStaticDumper\HttpFoundation\ControllerContentResolver;

final class DumpStaticSiteCommand extends Command
{
    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var string
     */
    private $publicDirectory;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var ControllerDumper
     */
    private $controllerDumper;

    public function __construct(
        RouterInterface $router,
        SymfonyStyle $symfonyStyle,
        ControllerContentResolver $controllerContentResolver,
        FinderSanitizer $finderSanitizer,
        ControllerDumper $controllerDumper,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();

        $this->publicDirectory = $parameterBag->get('kernel.project_dir') . '/public';
        $this->outputDirectory = getcwd() . '/output';

        $this->router = $router;
        $this->symfonyStyle = $symfonyStyle;
        $this->controllerContentResolver = $controllerContentResolver;
        $this->finderSanitizer = $finderSanitizer;
        $this->controllerDumper = $controllerDumper;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->section('Dumping static website');

        $this->controllerDumper->dump();

        $this->symfonyStyle->success(sprintf('Controllers generated to "%s"', $this->outputDirectory));

        $this->copyAssets();
        $this->symfonyStyle->success('Assets copied');

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

        return ShellCode::SUCCESS;
    }

    private function copyAssets(): void
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->publicDirectory)
            ->notName('*.php');

        $assetFileInfos = $this->finderSanitizer->sanitize($finder);
        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory($this->publicDirectory);

            FileSystem::copy($assetFileInfo->getRealPath(), $this->outputDirectory . '/' . $relativePathFromRoot);
        }
    }
}

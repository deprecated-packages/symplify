<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SymfonyStaticDumper\Configuration\SymfonyStaticDumperConfiguration;
use Symplify\SymfonyStaticDumper\Controller\ControllerDumper;

final class DumpStaticSiteCommand extends Command
{
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

    /**
     * @var SymfonyStaticDumperConfiguration
     */
    private $symfonyStaticDumperConfiguration;

    public function __construct(
        SymfonyStaticDumperConfiguration $symfonyStaticDumperConfiguration,
        SymfonyStyle $symfonyStyle,
        FinderSanitizer $finderSanitizer,
        ControllerDumper $controllerDumper
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->finderSanitizer = $finderSanitizer;
        $this->controllerDumper = $controllerDumper;
        $this->symfonyStaticDumperConfiguration = $symfonyStaticDumperConfiguration;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->section('Dumping static website');

        $this->controllerDumper->dump();

        $this->symfonyStyle->success(
            sprintf('Controllers generated to "%s"', $this->symfonyStaticDumperConfiguration->getOutputDirectory())
        );

        $this->copyAssets();
        $this->symfonyStyle->success('Assets copied');

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

        return ShellCode::SUCCESS;
    }

    private function copyAssets(): void
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->symfonyStaticDumperConfiguration->getPublicDirectory())
            ->notName('*.php');

        $assetFileInfos = $this->finderSanitizer->sanitize($finder);
        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory(
                $this->symfonyStaticDumperConfiguration->getPublicDirectory()
            );

            FileSystem::copy(
                $assetFileInfo->getRealPath(),
                $this->symfonyStaticDumperConfiguration->getOutputDirectory() . '/' . $relativePathFromRoot
            );
        }
    }
}

<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SymfonyStaticDumper\Configuration\SymfonyStaticDumperConfiguration;
use Symplify\SymfonyStaticDumper\Controller\ControllerDumper;
use Symplify\SymfonyStaticDumper\FileSystem\AssetsCopier;

final class DumpStaticSiteCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ControllerDumper
     */
    private $controllerDumper;

    /**
     * @var SymfonyStaticDumperConfiguration
     */
    private $symfonyStaticDumperConfiguration;

    /**
     * @var AssetsCopier
     */
    private $assetsCopier;

    public function __construct(
        SymfonyStaticDumperConfiguration $symfonyStaticDumperConfiguration,
        SymfonyStyle $symfonyStyle,
        ControllerDumper $controllerDumper,
        AssetsCopier $assetsCopier
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->controllerDumper = $controllerDumper;
        $this->symfonyStaticDumperConfiguration = $symfonyStaticDumperConfiguration;
        $this->assetsCopier = $assetsCopier;
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

        $this->assetsCopier->copyAssets();
        $this->symfonyStyle->success('Assets copied');

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

        return ShellCode::SUCCESS;
    }
}

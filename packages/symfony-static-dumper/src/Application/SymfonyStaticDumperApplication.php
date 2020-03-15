<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Application;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SymfonyStaticDumper\Controller\ControllerDumper;
use Symplify\SymfonyStaticDumper\FileSystem\AssetsCopier;

final class SymfonyStaticDumperApplication
{
    /**
     * @var ControllerDumper
     */
    private $controllerDumper;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var AssetsCopier
     */
    private $assetsCopier;

    public function __construct(
        ControllerDumper $controllerDumper,
        SymfonyStyle $symfonyStyle,
        AssetsCopier $assetsCopier
    ) {
        $this->controllerDumper = $controllerDumper;
        $this->symfonyStyle = $symfonyStyle;
        $this->assetsCopier = $assetsCopier;
    }

    public function run(string $publicDirectory, string $outputDirectory): void
    {
        $this->controllerDumper->dump($outputDirectory);
        $this->symfonyStyle->success(sprintf('Controllers generated to "%s"', $outputDirectory));

        $this->assetsCopier->copyAssets($publicDirectory, $outputDirectory);
        $this->symfonyStyle->success('Assets copied');
    }
}

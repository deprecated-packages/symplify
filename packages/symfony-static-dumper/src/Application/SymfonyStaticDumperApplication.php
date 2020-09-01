<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Application;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SymfonyStaticDumper\Controller\ControllerDumper;
use Symplify\SymfonyStaticDumper\FileSystem\AssetsCopier;

/**
 * @see \Symplify\SymfonyStaticDumper\Tests\Application\SymfonyStaticDumperApplicationTest
 */
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

        $message = sprintf('Files generated to "%s"', $outputDirectory);
        $this->symfonyStyle->success($message);

        $this->assetsCopier->copyAssets($publicDirectory, $outputDirectory);
        $this->symfonyStyle->success('Assets copied');
    }
}

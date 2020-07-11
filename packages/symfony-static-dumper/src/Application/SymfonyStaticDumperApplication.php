<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Application;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SymfonyStaticDumper\Controller\ControllerDumper;
use Symplify\SymfonyStaticDumper\Controller\RouteFilter\RouteNameFilter;
use Symplify\SymfonyStaticDumper\Controller\RouteFilter\WildcardFilter;
use Symplify\SymfonyStaticDumper\FileSystem\AssetsCopier;
use function count;

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

    public function dumpControllers(string $outputDirectory, $routeNames = []): void
    {
        $this->controllerDumper->dump(
            $outputDirectory,
            count($routeNames) === 0 ? new WildcardFilter() : new RouteNameFilter($routeNames)
        );

        $message = sprintf('Files generated to "%s"', $outputDirectory);
        $this->symfonyStyle->success($message);
    }

    public function copyAssets(string $publicDirectory, string $outputDirectory): void
    {
        $this->assetsCopier->copyAssets($publicDirectory, $outputDirectory);
        $this->symfonyStyle->success('Assets copied');
    }
}

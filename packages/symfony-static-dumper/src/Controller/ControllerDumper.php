<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Controller;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use Symplify\SymfonyStaticDumper\ControllerWithDataProviderMatcher;
use Symplify\SymfonyStaticDumper\FileSystem\FilePathResolver;
use Symplify\SymfonyStaticDumper\HttpFoundation\ControllerContentResolver;
use Symplify\SymfonyStaticDumper\Routing\RoutesProvider;

final class ControllerDumper
{
    /**
     * @var ControllerWithDataProviderMatcher
     */
    private $controllerWithDataProviderMatcher;

    /**
     * @var ControllerContentResolver
     */
    private $controllerContentResolver;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var RoutesProvider
     */
    private $routesProvider;

    /**
     * @var FilePathResolver
     */
    private $filePathResolver;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(
        ControllerWithDataProviderMatcher $controllerWithDataProviderMatcher,
        ControllerContentResolver $controllerContentResolver,
        RoutesProvider $routesProvider,
        SymfonyStyle $symfonyStyle,
        FilePathResolver $filePathResolver,
        SmartFileSystem $smartFileSystem
    ) {
        $this->controllerWithDataProviderMatcher = $controllerWithDataProviderMatcher;
        $this->controllerContentResolver = $controllerContentResolver;
        $this->symfonyStyle = $symfonyStyle;
        $this->routesProvider = $routesProvider;
        $this->filePathResolver = $filePathResolver;
        $this->smartFileSystem = $smartFileSystem;
    }

    public function dump(string $outputDirectory): void
    {
        $this->dumpControllerWithoutParametersContents($outputDirectory);
        $this->dumpControllerWithParametersContents($outputDirectory);
    }

    private function dumpControllerWithoutParametersContents(string $outputDirectory): void
    {
        $routesWithoutArguments = $this->routesProvider->provideRoutesWithoutArguments();

        $this->createProgressBarIfNeeded($routesWithoutArguments);

        foreach ($routesWithoutArguments as $routeName => $route) {
            $fileContent = $this->controllerContentResolver->resolveFromRoute($routeName, $route);
            if ($fileContent === null) {
                continue;
            }

            $filePath = $this->filePathResolver->resolveFilePath($route, $outputDirectory);
            $this->advance($route, $filePath);

            $this->smartFileSystem->dumpFile($filePath, $fileContent);
        }
    }

    private function dumpControllerWithParametersContents(string $outputDirectory): void
    {
        $routesWithParameters = $this->routesProvider->provideRoutesWithParameters();

        foreach ($routesWithParameters as $routeName => $route) {
            $controllerWithDataProvider = $this->controllerWithDataProviderMatcher->matchRoute($route);
            if (! $controllerWithDataProvider instanceof ControllerWithDataProviderInterface) {
                continue;
            }

            $this->printHeadline($controllerWithDataProvider, $routeName);

            $this->createProgressBarIfNeeded($controllerWithDataProvider->getArguments());

            $this->processControllerWithDataProvider(
                $controllerWithDataProvider,
                $routeName,
                $route,
                $outputDirectory
            );
        }
    }

    /**
     * @param mixed[] $items
     */
    private function createProgressBarIfNeeded(array $items): void
    {
        if ($this->symfonyStyle->isDebug()) {
            // show file names on debug, no progress bar
            return;
        }

        $stepCount = count($items);
        $this->symfonyStyle->progressStart($stepCount);
    }

    private function advance(Route $route, string $filePath): void
    {
        if ($this->symfonyStyle->isDebug()) {
            $message = sprintf('Dumping static content for "%s" route to "%s" path', $route->getPath(), $filePath);
            $this->symfonyStyle->note($message);
        } else {
            $this->symfonyStyle->progressAdvance();
        }
    }

    private function printHeadline(
        ControllerWithDataProviderInterface $controllerWithDataProvider,
        string $routeName
    ): void {
        $this->symfonyStyle->newLine(2);

        $message = sprintf(
            'Dumping data for "%s" data provider and "%s" route',
            get_class($controllerWithDataProvider),
            $routeName
        );

        $this->symfonyStyle->section($message);
    }

    private function processControllerWithDataProvider(
        ControllerWithDataProviderInterface $controllerWithDataProvider,
        string $routeName,
        Route $route,
        string $outputDirectory
    ): void {
        $arguments = $controllerWithDataProvider->getArguments();
        foreach ($arguments as $argument) {
            $fileContent = $this->controllerContentResolver->resolveFromRouteAndArgument(
                $routeName,
                $route,
                $argument
            );

            if ($fileContent === null) {
                continue;
            }

            $filePath = $this->filePathResolver->resolveFilePathWithArgument($route, $outputDirectory, $argument);
            $this->advance($route, $filePath);

            $this->smartFileSystem->dumpFile($filePath, $fileContent);
        }
    }
}

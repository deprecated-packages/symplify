<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Controller;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
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

    public function __construct(
        ControllerWithDataProviderMatcher $controllerWithDataProviderMatcher,
        ControllerContentResolver $controllerContentResolver,
        RoutesProvider $routesProvider,
        SymfonyStyle $symfonyStyle,
        FilePathResolver $filePathResolver
    ) {
        $this->controllerWithDataProviderMatcher = $controllerWithDataProviderMatcher;
        $this->controllerContentResolver = $controllerContentResolver;
        $this->symfonyStyle = $symfonyStyle;
        $this->routesProvider = $routesProvider;
        $this->filePathResolver = $filePathResolver;
    }

    public function dump(string $outputDirectory): void
    {
        $this->dumpControllerWithoutParametersContents($outputDirectory);
        $this->dumpControllerWithParametersContents($outputDirectory);
    }

    private function dumpControllerWithoutParametersContents($outputDirectory): void
    {
        $routesWithoutArguments = $this->routesProvider->provideRoutesWithoutArguments();

        $progressBar = $this->createProgressBarIfNeeded($routesWithoutArguments);

        foreach ($routesWithoutArguments as $routeName => $route) {
            $fileContent = $this->controllerContentResolver->resolveFromRoute($routeName, $route);
            if ($fileContent === null) {
                continue;
            }

            $filePath = $this->filePathResolver->resolveFilePath($route, $outputDirectory);

            if ($this->symfonyStyle->isDebug()) {
                $this->symfonyStyle->note(sprintf(
                    'Dumping static content for "%s" route to "%s" path',
                    $route->getPath(),
                    $filePath
                ));
            } elseif ($progressBar instanceof ProgressBar) {
                $progressBar->advance();
            }

            FileSystem::write($filePath, $fileContent);
        }
    }

    private function dumpControllerWithParametersContents(string $outputDirectory): void
    {
        $routesWithParameters = $this->routesProvider->provideRoutesWithParameters();

        foreach ($routesWithParameters as $routeName => $route) {
            $controllerWithDataProvider = $this->controllerWithDataProviderMatcher->matchRoute($route);
            if ($controllerWithDataProvider === null) {
                continue;
            }

            $this->printHeadline($controllerWithDataProvider, $routeName);

            $progressBar = $this->createProgressBarIfNeeded($controllerWithDataProvider->getArguments());

            $this->processControllerWithDataProvider(
                $controllerWithDataProvider,
                $routeName,
                $route,
                $outputDirectory,
                $progressBar
            );
        }
    }

    private function printHeadline(ControllerWithDataProviderInterface $controllerWithDataProvider, $routeName): void
    {
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->section(sprintf(
            'Dumping data for "%s" data provider and "%s" route',
            get_class($controllerWithDataProvider),
            $routeName
        ));
    }

    private function processControllerWithDataProvider(
        ControllerWithDataProviderInterface $controllerWithDataProvider,
        $routeName,
        $route,
        string $outputDirectory,
        ?ProgressBar $progressBar
    ): void {
        foreach ($controllerWithDataProvider->getArguments() as $argument) {
            $fileContent = $this->controllerContentResolver->resolveFromRouteAndArgument(
                $routeName,
                $route,
                $argument
            );

            if ($fileContent === null) {
                continue;
            }

            $filePath = $this->filePathResolver->resolveFilePathWithArgument($route, $outputDirectory, $argument);

            if ($this->symfonyStyle->isDebug()) {
                $this->symfonyStyle->note(sprintf(
                    'Dumping static content for "%s" route to "%s" path',
                    $route->getPath(),
                    $filePath
                ));
            } elseif ($progressBar instanceof ProgressBar) {
                $progressBar->advance();
            }

            FileSystem::write($filePath, $fileContent);
        }
    }

    private function createProgressBarIfNeeded(array $items): ?ProgressBar
    {
        if ($this->symfonyStyle->isDebug()) {
            // show file names on debug, no progress bar
            return null;
        }

        $stepCount = count($items);
        return $this->symfonyStyle->createProgressBar($stepCount);
    }
}

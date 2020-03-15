<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Controller;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;
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
        foreach ($this->routesProvider->provide() as $route) {
            // needs arguments
            if ($this->isRouteWithArguments($route)) {
                continue;
            }

            $fileContent = $this->controllerContentResolver->resolveFromRoute($route);
            if ($fileContent === null) {
                continue;
            }

            $filePath = $this->filePathResolver->resolveFilePath($route, $outputDirectory);

            $this->symfonyStyle->note(sprintf(
                'Dumping static content for "%s" route to "%s" path',
                $route->getPath(),
                $filePath
            ));

            FileSystem::write($filePath, $fileContent);
        }
    }

    private function dumpControllerWithParametersContents(string $outputDirectory): void
    {
        foreach ($this->routesProvider->provide() as $route) {
            // needs arguments
            if (! Strings::match($route->getPath(), '#\{(.*?)\}#sm')) {
                continue;
            }

            $controllerWithDataProvider = $this->controllerWithDataProviderMatcher->matchRoute($route);
            if ($controllerWithDataProvider === null) {
                continue;
            }

            foreach ($controllerWithDataProvider->getArguments() as $argument) {
                $fileContent = $this->controllerContentResolver->resolveFromRouteAndArgument($route, $argument);
                if ($fileContent === null) {
                    continue;
                }

                $filePath = $this->filePathResolver->resolveFilePathWithArgument($route, $outputDirectory, $argument);

                $this->symfonyStyle->note(sprintf(
                    'Dumping static content for "%s" route to "%s" path',
                    $route->getPath(),
                    $filePath
                ));

                FileSystem::write($filePath, $fileContent);
            }
        }
    }

    private function isRouteWithArguments(Route $route): bool
    {
        return (bool) Strings::match($route->getPath(), '#\{(.*?)\}#sm');
    }
}

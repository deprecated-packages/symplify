<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Controller;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symplify\SymfonyStaticDumper\ControllerWithDataProviderMatcher;
use Symplify\SymfonyStaticDumper\HttpFoundation\ControllerContentResolver;

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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        ControllerWithDataProviderMatcher $controllerWithDataProviderMatcher,
        ControllerContentResolver $controllerContentResolver,
        RouterInterface $router,
        SymfonyStyle $symfonyStyle
    ) {
        $this->controllerWithDataProviderMatcher = $controllerWithDataProviderMatcher;
        $this->controllerContentResolver = $controllerContentResolver;
        $this->router = $router;
        $this->symfonyStyle = $symfonyStyle;
    }

    public function dump(string $outputDirectory): void
    {
        $this->dumpControllerWithoutParametersContents($outputDirectory);
        $this->dumpControllerWithParametersContents($outputDirectory);
    }

    private function dumpControllerWithoutParametersContents($outputDirectory): void
    {
        foreach ($this->router->getRouteCollection() as $route) {
            // needs arguments
            if (Strings::match($route->getPath(), '#\{(.*?)\}#sm')) {
                continue;
            }

            $fileContent = $this->controllerContentResolver->resolveFromRoute($route);
            if ($fileContent === null) {
                continue;
            }

            $filePath = $this->resolveFilePath($route, $outputDirectory);

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
        foreach ($this->router->getRouteCollection() as $route) {
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

                $filePath = $this->resolveFilePathWithArgument($route, $outputDirectory, $argument);

                $this->symfonyStyle->note(sprintf(
                    'Dumping static content for "%s" route to "%s" path',
                    $route->getPath(),
                    $filePath
                ));

                FileSystem::write($filePath, $fileContent);
            }
        }
    }

    private function resolveFilePathWithArgument(Route $route, string $outputDirectory, ...$arguments): string
    {
        $filePath = $this->resolveFilePath($route, $outputDirectory);

        $i = 0;
        return Strings::replace($filePath, '#{(.*?)}#m', function ($match) use (&$i, $arguments) {
            $value = $arguments[$i];

            ++$i;

            return $value;
        });
    }

    private function resolveFilePath(Route $route, string $outputDirectory): string
    {
        $routePath = $route->getPath();
        $routePath = ltrim($routePath, '/');

        if ($routePath === '') {
            $routePath = 'index.html';
        } elseif (! $this->isFileWithSuffix($routePath)) {
            $routePath .= '/index.html';
        }

        return $outputDirectory . '/' . $routePath;
    }

    /**
     * E.g. some.xml
     */
    private function isFileWithSuffix(string $routePath): bool
    {
        return (bool) Strings::match($routePath, '#\.[\w]+#');
    }
}

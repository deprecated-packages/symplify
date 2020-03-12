<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SymfonyStaticDumper\HttpFoundation\ControllerContentResolver;

final class DumpStaticSiteCommand extends Command
{
    /**
     * @var string
     */
    public const PUBLIC_DIRECTORY = __DIR__ . '/../../../../public';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ControllerContentResolver
     */
    private $controllerContentResolver;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(
        RouterInterface $router,
        SymfonyStyle $symfonyStyle,
        ControllerContentResolver $controllerContentResolver,
        FinderSanitizer $finderSanitizer
    ) {
        parent::__construct();

        $this->router = $router;
        $this->symfonyStyle = $symfonyStyle;
        $this->controllerContentResolver = $controllerContentResolver;
        $this->finderSanitizer = $finderSanitizer;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->section('Dumping static website');

        $this->dumpControllerContents();
        $this->symfonyStyle->success('Controllers generated');

        $this->copyAssets();
        $this->symfonyStyle->success('Assets copied');

        return ShellCode::SUCCESS;
    }

    private function dumpControllerContents(): void
    {
        foreach ($this->router->getRouteCollection() as $route) {
            $fileContent = $this->controllerContentResolver->resolveFromRoute($route);
            if ($fileContent === null) {
                continue;
            }

            $filePath = $this->resolveFilePath($route);

            $this->symfonyStyle->note(sprintf(
                'Dumping static content for "%s" route to "%s" path',
                $route->getPath(),
                $filePath
            ));

            FileSystem::write($filePath, $fileContent);
        }
    }

    private function copyAssets(): void
    {
        $finder = new Finder();
        $finder->files()
            ->in(self::PUBLIC_DIRECTORY)
            ->notName('*.php');

        $assetFileInfos = $this->finderSanitizer->sanitize($finder);
        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory(self::PUBLIC_DIRECTORY);
            FileSystem::copy($assetFileInfo->getRealPath(), getcwd() . '/output/' . $relativePathFromRoot);
        }
    }

    private function resolveFilePath(Route $route): string
    {
        $routePath = $route->getPath();

        $routePath = ltrim($routePath, '/');

        if ($routePath === '') {
            $routePath = 'index.html';
        } elseif (! $this->isFileWithSuffix($routePath)) {
            $routePath .= '/index.html';
        }

        return getcwd() . '/output/' . $routePath;
    }

    private function isFileWithSuffix(string $routePath): bool
    {
        return (bool) Strings::match($routePath, '#\.[\w]+#');
    }
}

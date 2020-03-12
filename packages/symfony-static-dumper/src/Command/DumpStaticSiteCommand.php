<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    private $outputDirectory;

    /**
     * @var string
     */
    private $publicDirectory;

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
        ParameterBagInterface $parameterBag,
        RouterInterface $router,
        SymfonyStyle $symfonyStyle,
        ControllerContentResolver $controllerContentResolver,
        FinderSanitizer $finderSanitizer
    ) {
        parent::__construct();

        $this->publicDirectory = $parameterBag->get('kernel.project_dir') . '/public';
        $this->outputDirectory = getcwd() . '/output';

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
        $this->symfonyStyle->title('Dumping static website');

        $this->dumpControllerContents();
        $this->symfonyStyle->success(sprintf('Controllers generated to "%s"', $this->outputDirectory));

        $this->copyAssets();
        $this->symfonyStyle->success('Assets copied');

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

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
            ->in($this->publicDirectory)
            ->notName('*.php');

        $assetFileInfos = $this->finderSanitizer->sanitize($finder);
        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory($this->publicDirectory);

            FileSystem::copy($assetFileInfo->getRealPath(), $this->outputDirectory . '/' . $relativePathFromRoot);
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

        return $this->outputDirectory . '/' . $routePath;
    }

    private function isFileWithSuffix(string $routePath): bool
    {
        return (bool) Strings::match($routePath, '#\.[\w]+#');
    }
}

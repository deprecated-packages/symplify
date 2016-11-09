<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Application;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Application\Command\RunCommand;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\HttpServer\HttpServer;
use Symplify\PHP7_Sculpin\Output\FileSystemWriter;
use Symplify\PHP7_Sculpin\Renderable\Latte\DynamicStringLoader;
use Symplify\PHP7_Sculpin\Renderable\RenderableFilesProcessor;
use Symplify\PHP7_Sculpin\Source\SourceFileStorage;
use Symplify\PHP7_Sculpin\Utils\FilesystemChecker;

final class SculpinApplication
{
    /**
     * @var SourceFileStorage
     */
    private $sourceFileStorage;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @var RenderableFilesProcessor
     */
    private $renderableFilesProcessor;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    /**
     * @var HttpServer
     */
    private $httpServer;

    /**
     * @var string
     */
    private $sinceTime;

    /**
     * @var bool
     */
    private $shouldRegenerate = true;

    public function __construct(
        SourceFileStorage $sourceFileStorage,
        Configuration $configuration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        DynamicStringLoader $dynamicStringLoader,
        HttpServer $httpServer
    ) {
        $this->sourceFileStorage = $sourceFileStorage;
        $this->configuration = $configuration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->dynamicStringLoader = $dynamicStringLoader;
        $this->httpServer = $httpServer;
    }

    public function runCommand(RunCommand $runCommand)
    {
        $this->processCommand($runCommand);

        if ($runCommand->isRunServer()) {
            $this->httpServer->init();

            $this->httpServer->addPeriodicTimer(1, function () use ($runCommand) {
                clearstatcache();
                $this->processCommand($runCommand);
            });

            $this->httpServer->run();
        }
    }

    private function processCommand(RunCommand $runCommand)
    {
        $this->loadConfigurationWithDirectories($runCommand);

        FilesystemChecker::ensureDirectoryExists($runCommand->getSourceDirectory());

        $this->loadSourcesFromSourceDirectory($runCommand->getSourceDirectory());

        if (!$this->shouldRegenerate) {
            return;
        }

        // 1. copy static files
        $this->fileSystemWriter->copyStaticFiles($this->sourceFileStorage->getStaticFiles());

        // 2. collect configuration
        $this->configuration->loadFromFiles($this->sourceFileStorage->getConfigurationFiles());

        // 3. collect layouts
        $this->loadLayoutsToLatteLoader($this->sourceFileStorage->getLayoutFiles());

        // 4. completely process post
        $this->renderableFilesProcessor->processFiles($this->sourceFileStorage->getPostFiles());

        // 5. render files
        $this->renderableFilesProcessor->processFiles($this->sourceFileStorage->getRenderableFiles());

        $this->shouldRegenerate = false;
    }

    private function loadConfigurationWithDirectories(RunCommand $runCommand)
    {
        $this->configuration->setSourceDirectory($runCommand->getSourceDirectory());
        $this->configuration->setOutputDirectory($runCommand->getOutputDirectory());
    }

    private function loadSourcesFromSourceDirectory(string $sourceDirectory)
    {
        $files = $this->findFilesInSourceDirectory($sourceDirectory);
        $this->sourceFileStorage->loadSourcesFromFiles($files);
    }

    private function findFilesInSourceDirectory(string $sourceDirectory) : array
    {
        $sinceTimeLast = $this->sinceTime;
        $this->sinceTime = date('U');

        $finder = Finder::findFiles('*')->from($sourceDirectory);

        $files = [];
        foreach ($finder as $key => $file) {
            $files[$key] = $file;
        }

        foreach ($files as $key => $file) {
            if ($file->getMTime() >= $sinceTimeLast && $this->isGlobalFile($file)) {
                // global file has changed, regenerate all found files
                $this->shouldRegenerate = true;
                break;
            }

            /** @var SplFileInfo $file */
            if ($file->getMTime() > $sinceTimeLast) {
                // this file has changed, we need to regenerate it
                $this->shouldRegenerate = true;
            } elseif (!$this->isGlobalFile($file)) {
                // this file has not changed, nor is global, drop it
                unset($files[$key]);
            }
        }

        return $files;
    }

    /**
     * @param SplFileInfo[] $layoutFiles
     */
    private function loadLayoutsToLatteLoader(array $layoutFiles)
    {
        foreach ($layoutFiles as $layoutFile) {
            $name = $layoutFile->getBasename('.' . $layoutFile->getExtension());
            $content = file_get_contents($layoutFile->getRealPath());
            $this->dynamicStringLoader->addTemplate($name, $content);
        }
    }

    private function isGlobalFile(SplFileInfo $file) : bool
    {
        if (Strings::endsWith($file->getPath(), '_layouts')) {
            return true;
        }

        if ($file->getExtension() === 'neon') {
            return true;
        }

        return false;
    }
}

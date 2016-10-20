<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable;

use SplFileInfo;
use Symplify\PHP7_Sculpin\Output\FileSystemWriter;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Renderable\Configuration\ConfigurationDecorator;
use Symplify\PHP7_Sculpin\Renderable\File\File;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;
use Symplify\PHP7_Sculpin\Renderable\File\PostFile;
use Symplify\PHP7_Sculpin\Renderable\Latte\LatteDecorator;
use Symplify\PHP7_Sculpin\Renderable\Markdown\MarkdownDecorator;
use Symplify\PHP7_Sculpin\Renderable\Routing\RouteDecorator;

final class RenderableFilesProcessor
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var RouteDecorator
     */
    private $routeDecorator;

    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    /**
     * @var MarkdownDecorator
     */
    private $markdownDecorator;
    /**
     * @var LatteDecorator
     */
    private $latteDecorator;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        FileFactory $fileFactory,
        RouteDecorator $routeDecorator,
        ConfigurationDecorator $configurationDecorator,
        MarkdownDecorator $markdownDecorator,
        LatteDecorator $latteDecorator,
        FileSystemWriter $fileSystemWriter,
        Configuration $configuration
    ) {
        $this->fileFactory = $fileFactory;
        $this->routeDecorator = $routeDecorator;
        $this->configurationDecorator = $configurationDecorator;
        $this->markdownDecorator = $markdownDecorator;
        $this->latteDecorator = $latteDecorator;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->configuration = $configuration;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function processFiles(array $fileInfos)
    {
        if (!count($fileInfos)) {
            return;
        }

        $files = $this->createFileObjectsFromFileInfos($fileInfos);

        $this->setPostsToConfiguration($files);
        $this->setRoutesToFiles($files);

        $this->setFileConfigurationToFile($files);
        $this->formatFileContentFromMarkdownToHtml($files);
        $this->formatFileContentFromLatteToHtml($files);

        $this->fileSystemWriter->copyRenderableFiles($files);
    }

    /**
     * @param array $fileInfos
     *
     * @return array
     */
    private function createFileObjectsFromFileInfos(array $fileInfos):array
    {
        $files = [];
        foreach ($fileInfos as $id => $fileInfo) {
            $files[$id] = $this->fileFactory->create($fileInfo);
        }

        return $files;
    }

    /**
     * @param File[] $files
     */
    private function setPostsToConfiguration(array $files)
    {
        if (reset($files) instanceof PostFile) {
            $this->configuration->addOption('posts', $files);
        }
    }

    /**
     * @param File[] $files
     */
    private function setRoutesToFiles(array $files)
    {
        foreach ($files as $file) {
            $this->routeDecorator->decorateFile($file);
        }
    }

    /**
     * @param File[] $files
     */
    private function setFileConfigurationToFile(array $files)
    {
        foreach ($files as $file) {
            $this->configurationDecorator->decorateFile($file);
        }
    }

    /**
     * @param File[] $files
     */
    private function formatFileContentFromMarkdownToHtml(array $files)
    {
        foreach ($files as $file) {
            $this->markdownDecorator->decorateFile($file);
        }
    }

    /**
     * @param File[] $files
     */
    private function formatFileContentFromLatteToHtml(array $files)
    {
        foreach ($files as $file) {
            $this->latteDecorator->decorateFile($file);
        }
    }
}

<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Template;

use Symplify\MigrifyKernel\Exception\ShouldNotHappenException;
use Symplify\TemplateChecker\Finder\GenericFilesFinder;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class TemplatePathsResolver
{
    /**
     * @var GenericFilesFinder
     */
    private $genericFilesFinder;

    public function __construct(GenericFilesFinder $genericFilesFinder)
    {
        $this->genericFilesFinder = $genericFilesFinder;
    }

    /**
     * @param string[] $directories
     * @return string[]
     */
    public function resolveFromDirectories(array $directories): array
    {
        $twigTemplateFileInfos = $this->genericFilesFinder->find($directories, '*.twig');
        return $this->resolveTemplatePathsWithBundle($twigTemplateFileInfos);
    }

    /**
     * @param SmartFileInfo[] $twigTemplateFileInfos
     * @return string[]
     */
    private function resolveTemplatePathsWithBundle(array $twigTemplateFileInfos)
    {
        $templatePathsWithBundle = [];
        foreach ($twigTemplateFileInfos as $templateFileInfo) {
            $relativeTemplateFilepath = $this->resolveRelativeTemplateFilepath($templateFileInfo);
            $bundlePrefix = $this->findBundlePrefix($templateFileInfo);

            $templatePathsWithBundle[] = '@' . $bundlePrefix . '/' . $relativeTemplateFilepath;
        }

        sort($templatePathsWithBundle);

        return $templatePathsWithBundle;
    }

    /**
     * @return string
     */
    private function findBundlePrefix(SmartFileInfo $templateFileInfo)
    {
        $templateRealPath = $templateFileInfo->getRealPath();

        $bundleFileInfo = null;
        $currentDirectory = dirname($templateRealPath);
        do {
            /** @var string[] $foundFiles */
            $foundFiles = glob($currentDirectory . '/*Bundle.php');
            if ($foundFiles !== []) {
                $bundleFileRealPath = $foundFiles[0];

                $match = Strings::match($bundleFileRealPath, '#\/(?<bundle_name>[\w]+)Bundle\.php$#');
                if (! isset($match['bundle_name'])) {
                    throw new ShouldNotHappenException();
                }

                return $match['bundle_name'];
            }

            $currentDirectory = dirname($currentDirectory);
            // root dir, stop!
            if ($currentDirectory === '/') {
                break;
            }
        } while ($bundleFileInfo === null);

        throw new ShouldNotHappenException();
    }

    private function resolveRelativeTemplateFilepath(SmartFileInfo $templateFileInfo)
    {
        $match = Strings::match($templateFileInfo->getRealPath(), '#(views|template)/(?<template_relative_path>.*?)$#');
        if (! isset($match['template_relative_path'])) {
            throw new ShouldNotHappenException();
        }

        return $match['template_relative_path'];
    }
}

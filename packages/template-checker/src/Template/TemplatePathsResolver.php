<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Template;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class TemplatePathsResolver
{
    /**
     * @see https://regex101.com/r/dAH2eR/1
     * @var string
     */
    private const TEMPLATE_PATH_REGEX = '#(views|template)\/(?<template_relative_path>.*?)$#';

    /**
     * @see https://regex101.com/r/1xa9Ey/1
     * @var string
     */
    private const BUNDLE_NAME_REGEX = '#\/(?<bundle_name>[\w]+)Bundle\.php$#';

    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(SmartFinder $smartFinder)
    {
        $this->smartFinder = $smartFinder;
    }

    /**
     * @param string[] $directories
     * @return string[]
     */
    public function resolveFromDirectories(array $directories): array
    {
        $twigTemplateFileInfos = $this->smartFinder->find($directories, '*.twig');
        return $this->resolveTemplatePathsWithBundle($twigTemplateFileInfos);
    }

    /**
     * @param SmartFileInfo[] $twigTemplateFileInfos
     * @return string[]
     */
    private function resolveTemplatePathsWithBundle(array $twigTemplateFileInfos): array
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

    private function findBundlePrefix(SmartFileInfo $templateFileInfo): string
    {
        $templateRealPath = $templateFileInfo->getRealPath();

        $bundleFileInfo = null;
        $currentDirectory = dirname($templateRealPath);
        do {
            /** @var string[] $foundFiles */
            $foundFiles = glob($currentDirectory . '/*Bundle.php');
            if ($foundFiles !== []) {
                $bundleFileRealPath = $foundFiles[0];

                $match = Strings::match($bundleFileRealPath, self::BUNDLE_NAME_REGEX);
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

    private function resolveRelativeTemplateFilepath(SmartFileInfo $templateFileInfo): string
    {
        $match = Strings::match($templateFileInfo->getRealPath(), self::TEMPLATE_PATH_REGEX);
        if (! isset($match['template_relative_path'])) {
            throw new ShouldNotHappenException();
        }

        return $match['template_relative_path'];
    }
}

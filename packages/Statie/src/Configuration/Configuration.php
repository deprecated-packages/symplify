<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Configuration;

use SplFileInfo;
use Symplify\Statie\Configuration\Parser\NeonParser;


final class Configuration
{
    /**
     * @var string
     */
    const DEFAULT_POST_ROUTE = 'blog/:year/:month/:day/:title';

    /**
     * @var array
     */
    private $globalVariables = [];

    /**
     * @var NeonParser
     */
    private $neonParser;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var string
     */
    private $postRoute = self::DEFAULT_POST_ROUTE;

    /**
     * @var string
     */
    private $githubRepositorySlug;

    public function __construct(NeonParser $neonParser)
    {
        $this->neonParser = $neonParser;
    }

    /**
     * @param SplFileInfo[] $files
     */
    public function loadFromFiles(array $files)
    {
        foreach ($files as $file) {
            $decodedOptions = $this->neonParser->decodeFromFile($file->getRealPath());
            $decodedOptions = $this->extractPostRoute($decodedOptions);
            $decodedOptions = $this->extractGithubRepositorySlug($decodedOptions);
            $this->globalVariables = array_merge($this->globalVariables, $decodedOptions);
        }
    }

    /**
     * @param string       $name
     * @param string|array $value
     */
    public function addGlobalVarialbe(string $name, $value)
    {
        $this->globalVariables[$name] = $value;
    }

    public function getGlobalVariables() : array
    {
        return $this->globalVariables;
    }

    public function setSourceDirectory(string $sourceDirectory)
    {
        $this->sourceDirectory = $sourceDirectory;
    }

    public function getSourceDirectory() : string
    {
        if ($this->sourceDirectory) {
            return $this->sourceDirectory;
        }

        return getcwd() . DIRECTORY_SEPARATOR . 'source';
    }

    public function setOutputDirectory(string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }

    public function getOutputDirectory() : string
    {
        return $this->outputDirectory;
    }

    public function setPostRoute(string $postRoute)
    {
        $this->postRoute = $postRoute;
    }

    public function getPostRoute() : string
    {
        return $this->postRoute;
    }

    public function setGithubRepositorySlug(string $githubRepositorySlug)
    {
        $this->githubRepositorySlug = $githubRepositorySlug;
    }

    public function getGithubRepositorySlug() : string
    {
        return $this->githubRepositorySlug;
    }

    private function extractPostRoute(array $options) : array
    {
        if (! isset($options['configuration']['postRoute'])) {
            return $options;
        }

        $this->setPostRoute($options['configuration']['postRoute']);
        unset($options['configuration']['postRoute']);

        return $options;
    }


    private function extractGithubRepositorySlug(array $options) : array
    {
        if (! isset($options['configuration']['githubRepositorySlug'])) {
            return $options;
        }

        $this->setGithubRepositorySlug($options['configuration']['githubRepositorySlug']);
        unset($options['configuration']['githubRepositorySlug']);

        return $options;
    }
}

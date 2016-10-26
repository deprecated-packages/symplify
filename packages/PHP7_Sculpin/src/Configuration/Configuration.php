<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Configuration;

use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;

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
     * @var YamlAndNeonParser
     */
    private $yamlAndNeonParser;

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

    public function __construct(YamlAndNeonParser $yamlAndNeonParser)
    {
        $this->yamlAndNeonParser = $yamlAndNeonParser;
    }

    /**
     * @param SplFileInfo[] $files
     */
    public function loadFromFiles(array $files)
    {
        foreach ($files as $file) {
            $decodedOptions = $this->yamlAndNeonParser->decodeFromFile($file->getRealPath());
            $this->globalVariables += $this->extractPostRoute($decodedOptions);
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
        return $this->sourceDirectory;
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

    private function extractPostRoute(array $options) : array
    {
        if (!isset($options['configuration']['postRoute'])) {
            return $options;
        }

        $this->setPostRoute($options['configuration']['postRoute']);
        unset($options['configuration']['postRoute']);

        return $options;
    }
}

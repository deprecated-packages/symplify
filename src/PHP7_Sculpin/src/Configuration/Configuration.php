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
     * @var array
     */
    private $options = [];

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

    public function __construct(YamlAndNeonParser $yamlAndNeonParser)
    {
        $this->yamlAndNeonParser = $yamlAndNeonParser;
    }

    /**
     * @param SplFileInfo[] $files
     */
    public function loadOptionsFromFiles(array $files)
    {
        foreach ($files as $file) {
            $fileContent = file_get_contents($file->getRealPath());
            $this->options += $this->yamlAndNeonParser->decode($fileContent);
        }
    }

    /**
     * @param string       $name
     * @param string|array $value
     */
    public function addOption(string $name, $value)
    {
        $this->options[$name] = $value;
    }

    public function getOptions() : array
    {
        return $this->options;
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
}

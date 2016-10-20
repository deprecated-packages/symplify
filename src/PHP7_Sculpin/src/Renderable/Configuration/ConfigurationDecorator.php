<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\Configuration;

use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;
use Symplify\PHP7_Sculpin\Renderable\File\File;

final class ConfigurationDecorator
{
    /**
     * @var YamlAndNeonParser
     */
    private $yamlAndNeonParser;

    public function __construct(YamlAndNeonParser $yamlAndNeonParser)
    {
        $this->yamlAndNeonParser = $yamlAndNeonParser;
    }

    public function decorateFile(File $file)
    {
        if (preg_match('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', $file->getContent(), $matches)) {
            $file->changeContent($matches[2]);

            $this->setConfigurationToFileIfFoundAny($matches[1], $file);
        }
    }

    private function setConfigurationToFileIfFoundAny(string $content, File $file)
    {
        if (!preg_match('/^(\s*[-]+\s*|\s*)$/', $content)) {
            $configuration = $this->yamlAndNeonParser->decode($content);
            $file->setConfiguration($configuration);
        }
    }
}

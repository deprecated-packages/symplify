<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Renderable\Configuration;

use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\AbstractFile;

final class ConfigurationDecorator
{
    /**
     * @var NeonParser
     */
    private $yamlAndNeonParser;

    public function __construct(NeonParser $yamlAndNeonParser)
    {
        $this->yamlAndNeonParser = $yamlAndNeonParser;
    }

    public function decorateFile(AbstractFile $file)
    {
        if (preg_match('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', $file->getContent(), $matches)) {
            $file->changeContent($matches[2]);

            $this->setConfigurationToFileIfFoundAny($matches[1], $file);
        }
    }

    private function setConfigurationToFileIfFoundAny(string $content, AbstractFile $file)
    {
        if (! preg_match('/^(\s*[-]+\s*|\s*)$/', $content)) {
            $configuration = $this->yamlAndNeonParser->decode($content);
            $file->setConfiguration($configuration);
        }
    }
}

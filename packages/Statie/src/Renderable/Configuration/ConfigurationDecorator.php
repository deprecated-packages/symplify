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
    private $neonParser;

    public function __construct(NeonParser $neonParser)
    {
        $this->neonParser = $neonParser;
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
            $configuration = $this->neonParser->decode($content);
            $file->setConfiguration($configuration);
        }
    }
}

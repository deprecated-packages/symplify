<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\Configuration;

use Nette\Utils\Json;
use Symplify\MultiCodingStandard\Exception\Configuration\MultiCsFileNotFoundException;

final class MultiCsFileLoader
{
    /**
     * @var string
     */
    private $multiCsJsonFile;

    public function __construct(string $multiCsJsonFile = null)
    {
        $this->multiCsJsonFile = $multiCsJsonFile ?: getcwd().DIRECTORY_SEPARATOR.'multi-cs.json';
    }

    public function load() : array
    {
        $this->ensureFileExists($this->multiCsJsonFile);

        $fileContent = file_get_contents($this->multiCsJsonFile);

        return Json::decode($fileContent, true);
    }

    private function ensureFileExists(string $multiCsJsonFile)
    {
        if (!file_exists($multiCsJsonFile)) {
            throw new MultiCsFileNotFoundException(
                sprintf(
                    'File "%s" was not found in "%s". Did you forget to create it?',
                    'multi-cs.json',
                    realpath(dirname($multiCsJsonFile)).'/'.basename($multiCsJsonFile)
                )
            );
        }
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Translation\Filesystem;

use Nette\Utils\Finder;
use Nette\Utils\Strings;

final class ResourceFinder
{
    public function findInDirectory(string $directory) : array
    {
        $finder = Finder::findFiles('*.*.yml')->in($directory);
        $resources = [];

        foreach ($finder as $file) {
            /** @var \SplFileInfo $file */
            if (!$m = Strings::match(
                $file->getFilename(),
                '~^(?P<domain>.*?)\.(?P<locale>[^\.]+)\.(?P<format>[^\.]+)$~')
            ) {
                continue;
            }

            $resources[] = [
                'format' => $m['format'],
                'pathname' => $file->getPathname(),
                'locale' => $m['locale'],
                'domain' => $m['domain'],
            ];
        }

        return $resources;
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Configuration\Parser;

use Nette\Neon\Neon;
use Symfony\Component\Yaml\Yaml;
use Throwable;

final class YamlAndNeonParser
{
    public function decode(string $content) : array
    {
        try {
            return Neon::decode($content);
        } catch (Throwable $throwable) {
            return Yaml::parse($content);
        }
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\NetteAdapterForSymfonyBundles\Utils;

use Nette\Utils\Strings;

final class Naming
{
    public static function sanitazeClassName(string $name) : string
    {
        $name = Strings::webalize($name, '.');
        $name = strtr($name, ['-' => '_']);

        return $name;
    }
}

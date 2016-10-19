<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\AutoServiceRegistration\Naming;

final class ServiceNaming
{
    public static function createServiceIdFromClass(string $class) : string
    {
        return strtr(strtolower($class), [
            '\\' => '.',
        ]);
    }
}

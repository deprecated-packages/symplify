<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace SymplifyCodingStandard\Sniffs\Debug;

use Generic_Sniffs_PHP_ForbiddenFunctionsSniff;

/**
 * Rules:
 * - Debug functions should not be left in the code.
 *
 * @author Mikulas Dite <mikulas@dite.pro>
 */
class DebugFunctionCallSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{
    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|NULL)
     */
    public $forbiddenFunctions = [
        'd' => null,
        'dd' => null,
        'dump' => null,
        'var_dump' => null,
    ];
}

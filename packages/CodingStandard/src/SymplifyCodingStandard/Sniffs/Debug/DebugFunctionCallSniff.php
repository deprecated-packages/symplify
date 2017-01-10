<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Debug;

use Generic_Sniffs_PHP_ForbiddenFunctionsSniff;

/**
 * Rules:
 * - Debug functions should not be left in the code
 */
final class DebugFunctionCallSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{
    /**
     * @var string
     */
    const NAME = 'SymplifyCodingStandard.Debug.DebugFunctionCall';

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
        'var_dump' => null
    ];
}

<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;

/**
 * Debug functions should not be left in the code.
 */
final class DebugFunctionCallSniff extends ForbiddenFunctionsSniff
{
    /**
     * A list of forbidden functions with their alternatives.
     * The value is NULL if no alternative exists => the function should just not be used.
     *
     * @var mixed[]
     */
    public $forbiddenFunctions = [
        'd' => null,
        'dd' => null,
        'dump' => null,
        'var_dump' => null,
    ];
}

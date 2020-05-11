<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Debug;

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use Symplify\CodingStandard\Rules\NoDebugFuncCallRule;

/**
 * @deprecated
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

    public function __construct()
    {
        trigger_error(sprintf(
            'Sniff "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use "%s" instead',
            self::class,
            NoDebugFuncCallRule::class
        ));

        sleep(3);
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

final class RegexFuncCallAnalyzer
{
    /**
     * @var string[]
     */
    private const FUNC_CALLS_WITH_FIRST_ARG_REGEX = [
        'preg_match',
        'preg_match_all',
        'preg_split',
        'preg_replace',
        'preg_replace_callback',
    ];

    public function isRegexFuncCall(FuncCall $funcCall): bool
    {
        if (! $funcCall->name instanceof Name) {
            return false;
        }

        $funcCallName = $funcCall->name->toString();
        return in_array($funcCallName, self::FUNC_CALLS_WITH_FIRST_ARG_REGEX, true);
    }
}

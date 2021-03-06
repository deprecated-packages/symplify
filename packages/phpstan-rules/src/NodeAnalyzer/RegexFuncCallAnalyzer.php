<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use Symplify\Astral\Naming\SimpleNameResolver;

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

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function isRegexFuncCall(FuncCall $funcCall): bool
    {
        if ($funcCall->name instanceof Expr) {
            return false;
        }

        return $this->simpleNameResolver->isNames($funcCall->name, self::FUNC_CALLS_WITH_FIRST_ARG_REGEX);
    }
}

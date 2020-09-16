<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Contract;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;

interface RegexRuleInterface
{
    /**
     * @return string[]
     */
    public function processRegexFuncCall(FuncCall $funcCall): array;

    /**
     * @return string[]
     */
    public function processRegexStaticCall(StaticCall $staticCall): array;
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class RegexStaticCallAnalyzer
{
    /**
     * @var string[]
     */
    private const NETTE_UTILS_CALLS_METHOD_NAMES_WITH_SECOND_ARG_REGEX = ['match', 'matchAll', 'replace', 'split'];

    public function isRegexStaticCall(StaticCall $staticCall): bool
    {
        if (! $staticCall->class instanceof Name) {
            return false;
        }

        if ($staticCall->class->toString() !== 'Nette\Utils\Strings') {
            return false;
        }

        if (! $staticCall->name instanceof Identifier) {
            return false;
        }

        $staticCallName = $staticCall->name->toString();
        return in_array($staticCallName, self::NETTE_UTILS_CALLS_METHOD_NAMES_WITH_SECOND_ARG_REGEX, true);
    }
}

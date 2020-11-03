<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule\NoInlineStringRegexRuleTest
 */
final class NoInlineStringRegexRule extends AbstractRegexRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use local named constant instead of inline string for regex to explain meaning by constant name';

    /**
     * @return string[]
     */
    public function processRegexFuncCall(FuncCall $funcCall): array
    {
        $firstArgValue = $funcCall->args[0]->value;

        // it's not string → good
        if (! $firstArgValue instanceof String_) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return string[]
     */
    public function processRegexStaticCall(StaticCall $staticCall): array
    {
        $secondArgValue = $staticCall->args[1]->value;

        // it's not string → good
        if (! $secondArgValue instanceof String_) {
            return [];
        }

        $regexValue = $secondArgValue->value;

        if (Strings::length($regexValue) <= 7) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

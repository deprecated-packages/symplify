<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run($value)
    {
        return preg_match('#some_stu|ff#', $value);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var string
     */
    public const SOME_STUFF_REGEX = '#some_stu|ff#';

    public function run($value)
    {
        return preg_match(self::SOME_STUFF_REGEX, $value);
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}

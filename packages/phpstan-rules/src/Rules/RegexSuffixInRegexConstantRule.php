<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RegexSuffixInRegexConstantRule\RegexSuffixInRegexConstantRuleTest
 */
final class RegexSuffixInRegexConstantRule extends AbstractRegexRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Name your constant with "_REGEX" suffix, instead of "%s"';

    /**
     * @return string[]
     */
    public function processRegexFuncCall(FuncCall $funcCall): array
    {
        $firstArgValue = $funcCall->args[0]->value;
        return $this->processConstantName($firstArgValue);
    }

    /**
     * @return string[]
     */
    public function processRegexStaticCall(StaticCall $staticCall): array
    {
        $secondArgValue = $staticCall->args[1]->value;
        return $this->processConstantName($secondArgValue);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public const SOME_NAME = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME, $value);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public const SOME_NAME_REGEX = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME_REGEX, $value);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function processConstantName(Expr $expr): array
    {
        if (! $expr instanceof ClassConstFetch) {
            return [];
        }

        if ($expr->name instanceof Expr) {
            return [];
        }

        $constantName = (string) $expr->name;
        if (Strings::endsWith($constantName, '_REGEX')) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $constantName);
        return [$errorMessage];
    }
}

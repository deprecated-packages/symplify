<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\RegexSuffixInRegexConstantRule\RegexSuffixInRegexConstantRuleTest
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

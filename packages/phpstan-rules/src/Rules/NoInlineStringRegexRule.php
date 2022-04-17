<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\NodeAnalyzer\RegexFuncCallAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\RegexStaticCallAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoInlineStringRegexRule\NoInlineStringRegexRuleTest
 */
final class NoInlineStringRegexRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use local named constant instead of inline string for regex to explain meaning by constant name';

    public function __construct(
        private RegexFuncCallAnalyzer $regexFuncCallAnalyzer,
        private RegexStaticCallAnalyzer $regexStaticCallAnalyzer
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return CallLike::class;
    }

    /**
     * @param Node\Expr\CallLike $node
     * @return mixed[]|string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof FuncCall) {
            return $this->processRegexFuncCall($node);
        }

        if ($node instanceof StaticCall) {
            return $this->processRegexStaticCall($node);
        }

        return [];
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

    /**
     * @return string[]
     */
    private function processRegexFuncCall(FuncCall $funcCall): array
    {
        if (! $this->regexFuncCallAnalyzer->isRegexFuncCall($funcCall)) {
            return [];
        }

        $firstArgOrVariadicPlaceholder = $funcCall->args[0];
        if (! $firstArgOrVariadicPlaceholder instanceof Arg) {
            return [];
        }

        // it's not string → good
        if (! $firstArgOrVariadicPlaceholder->value instanceof String_) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return string[]
     */
    private function processRegexStaticCall(StaticCall $staticCall): array
    {
        if (! $this->regexStaticCallAnalyzer->isRegexStaticCall($staticCall)) {
            return [];
        }

        $argOrVariadicPlaceholder = $staticCall->args[1];
        if (! $argOrVariadicPlaceholder instanceof Arg) {
            return [];
        }

        $secondArgValue = $argOrVariadicPlaceholder->value;

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

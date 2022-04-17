<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\RegexFuncCallAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\RegexStaticCallAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RegexSuffixInRegexConstantRule\RegexSuffixInRegexConstantRuleTest
 */
final class RegexSuffixInRegexConstantRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Name your constant with "_REGEX" suffix, instead of "%s"';

    public function __construct(
        private RegexFuncCallAnalyzer $regexFuncCallAnalyzer,
        private RegexStaticCallAnalyzer $regexStaticCallAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string
    {
        return Expr\CallLike::class;
    }

    /**
     * @param Expr\CallLike $node
     * @return mixed[]|string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof FuncCall) {
            return $this->processFuncCall($node);
        }

        if ($node instanceof StaticCall) {
            return $this->processStaticCall($node);
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
        if (\str_ends_with($constantName, '_REGEX')) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $constantName);
        return [$errorMessage];
    }

    /**
     * @return string[]
     */
    private function processStaticCall(StaticCall $staticCall): array
    {
        if (! $this->regexStaticCallAnalyzer->isRegexStaticCall($staticCall)) {
            return [];
        }

        $argOrVariadicPlaceholder = $staticCall->args[1];
        if (! $argOrVariadicPlaceholder instanceof Arg) {
            return [];
        }

        return $this->processConstantName($argOrVariadicPlaceholder->value);
    }

    /**
     * @return string[]
     */
    private function processFuncCall(FuncCall $funcCall): array
    {
        if (! $this->regexFuncCallAnalyzer->isRegexFuncCall($funcCall)) {
            return [];
        }

        $argOrVariadicPlaceholder = $funcCall->args[0];
        if (! $argOrVariadicPlaceholder instanceof Arg) {
            return [];
        }

        $firstArgValue = $argOrVariadicPlaceholder->value;
        return $this->processConstantName($firstArgValue);
    }
}

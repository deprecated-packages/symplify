<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Rules;

use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * Based on https://www.sonarsource.com/docs/CognitiveComplexity.pdf
 *
 * A Cognitive Complexity score has 3 rules:
 * - B1. Ignore structures that allow multiple statements to be readably shorthanded into one
 * - B2. Increment (add one) for each break in the linear flow of the code
 * - B3. Increment when flow-breaking structures are nested
 *
 * @see https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/
 *
 * @see \Symplify\PHPStanRules\Tests\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule\FunctionLikeCognitiveComplexityRuleTest
 */
final class FunctionLikeCognitiveComplexityRule implements Rule, DocumentedRuleinterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Cognitive complexity for "%s" is %d, keep it under %d';

    public function __construct(
        private AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer,
        private int $maxMethodCognitiveComplexity = 8
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof ClassMethod && ! $node instanceof Function_) {
            return [];
        }

        $functionLikeCognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeFunctionLike($node);
        if ($functionLikeCognitiveComplexity <= $this->maxMethodCognitiveComplexity) {
            return [];
        }

        $functionLikeName = $this->resolveFunctionName($node, $scope);

        $message = sprintf(
            self::ERROR_MESSAGE,
            $functionLikeName,
            $functionLikeCognitiveComplexity,
            $this->maxMethodCognitiveComplexity
        );

        return [$message];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Cognitive complexity of function/method must be under specific limit',
            [new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                return false;
            }
        }

        return true;
    }
}
CODE_SAMPLE
            ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function simple($value)
    {
        if ($value === 1) {
            return true;
        }

        return $value === 2;
    }
}
CODE_SAMPLE
                ,
                [
                    'maxMethodCognitiveComplexity' => 5,
                ]
            )]
        );
    }

    private function resolveFunctionName(FunctionLike $functionLike, Scope $scope): string
    {
        if ($functionLike instanceof Function_) {
            return $functionLike->name . '()';
        }

        if ($functionLike instanceof ClassMethod) {
            $name = '';

            $classReflection = $scope->getClassReflection();
            if ($classReflection !== null) {
                $name = $classReflection->getName() . '::';
            }

            return $name . $functionLike->name . '()';
        }

        if ($functionLike instanceof Closure) {
            return 'closure';
        }

        if ($functionLike instanceof ArrowFunction) {
            return 'arrow function';
        }

        throw new ShouldNotHappenException();
    }
}

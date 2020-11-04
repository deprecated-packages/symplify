<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
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
 * @see \Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule\FunctionLikeCognitiveComplexityRuleTest
 */
final class FunctionLikeCognitiveComplexityRule extends AbstractSymplifyRule implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Cognitive complexity for "%s" is %d, keep it under %d';

    /**
     * @var int
     */
    private $maxMethodCognitiveComplexity;

    /**
     * @var AstCognitiveComplexityAnalyzer
     */
    private $astCognitiveComplexityAnalyzer;

    public function __construct(
        AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer,
        int $maxMethodCognitiveComplexity = 8
    ) {
        $this->maxMethodCognitiveComplexity = $maxMethodCognitiveComplexity;
        $this->astCognitiveComplexityAnalyzer = $astCognitiveComplexityAnalyzer;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class];
    }

    /**
     * @param Function_|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
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
            [new CodeSample(
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
```
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

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\BooleanType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Naming\BoolishNameAnalyser;
use Symplify\PHPStanRules\NodeFinder\ReturnNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\BoolishClassMethodPrefixRuleTest
 */
final class BoolishClassMethodPrefixRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" returns bool type, so the name should start with is/has/was...';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var BoolishNameAnalyser
     */
    private $boolishNameAnalyser;

    /**
     * @var ReturnNodeFinder
     */
    private $returnNodeFinder;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        BoolishNameAnalyser $boolishNameAnalyser,
        ReturnNodeFinder $returnNodeFinder
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->boolishNameAnalyser = $boolishNameAnalyser;
        $this->returnNodeFinder = $returnNodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        if ($this->shouldSkip($node, $scope, $classReflection)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, (string) $node->name);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function isOld(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(ClassMethod $classMethod, Scope $scope, ClassReflection $classReflection): bool
    {
        /** @var string $classMethodName */
        $classMethodName = $this->simpleNameResolver->getName($classMethod);

        $returns = $this->returnNodeFinder->findReturnsWithValues($classMethod);
        // nothing was returned
        if ($returns === []) {
            return true;
        }

        $methodReflection = $classReflection->getNativeMethod($classMethodName);
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())
            ->getReturnType();
        if (! $returnType instanceof BooleanType && ! $this->areOnlyBoolReturnNodes($returns, $scope)) {
            return true;
        }

        if ($this->boolishNameAnalyser->isBoolish($classMethodName)) {
            return true;
        }

        // is required by an interface
        return $this->isMethodRequiredByParentInterface($classReflection, $classMethodName);
    }

    /**
     * @param Return_[] $returns
     */
    private function areOnlyBoolReturnNodes(array $returns, Scope $scope): bool
    {
        foreach ($returns as $return) {
            if ($return->expr === null) {
                continue;
            }

            $returnedNodeType = $scope->getType($return->expr);
            if (! $returnedNodeType instanceof BooleanType) {
                return false;
            }
        }

        return true;
    }

    private function isMethodRequiredByParentInterface(ClassReflection $classReflection, string $methodName): bool
    {
        $interfaces = $classReflection->getInterfaces();
        foreach ($interfaces as $interface) {
            if ($interface->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}

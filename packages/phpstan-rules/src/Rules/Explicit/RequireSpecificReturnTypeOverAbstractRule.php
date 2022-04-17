<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeFinder\ReturnNodeFinder;
use Symplify\PHPStanRules\Reflection\MethodNodeAnalyser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\RequireSpecificReturnTypeOverAbstractRuleTest
 */
final class RequireSpecificReturnTypeOverAbstractRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Provide more specific return type "%s" over abstract one';

    public function __construct(
        private ReturnNodeFinder $returnNodeFinder,
        private MethodNodeAnalyser $methodNodeAnalyser,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        // @see https://stitcher.io/blog/php-8-named-arguments#named-arguments-in-depth
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class IssueControlFactory
{
    public function create(): Control
    {
        return new IssueControl();
    }
}

final class IssueControl extends Control
{
}
CODE_SAMPLE
             ,
                <<<'CODE_SAMPLE'
final class IssueControlFactory
{
    public function create(): IssueControl
    {
        return new IssueControl();
    }
}

final class IssueControl extends Control
{
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->returnType instanceof FullyQualified) {
            return [];
        }

        if ($this->shouldSkipScope($scope)) {
            return [];
        }

        $returnObjectType = new ObjectType($node->returnType->toString());
        if ($this->shouldSkipReturnObjectType($returnObjectType)) {
            return [];
        }

        /** @var string $methodName */
        $methodName = $this->simpleNameResolver->getName($node);
        if ($this->methodNodeAnalyser->hasParentVendorLock($scope, $methodName)) {
            return [];
        }

        $returnExpr = $this->returnNodeFinder->findOnlyReturnsExpr($node);
        if (! $returnExpr instanceof Expr) {
            return [];
        }

        $returnExprType = $scope->getType($returnExpr);
        if ($this->shouldSkipReturnExprType($returnExprType)) {
            return [];
        }

        if ($returnObjectType->equals($returnExprType)) {
            return [];
        }

        // is subtype?
        if (! $returnObjectType->isSuperTypeOf($returnExprType)->yes()) {
            return [];
        }

        /** @var TypeWithClassName $returnExprType */
        $errorMessage = sprintf(self::ERROR_MESSAGE, $returnExprType->getClassName());
        return [$errorMessage];
    }

    private function shouldSkipReturnObjectType(ObjectType $objectType): bool
    {
        $classReflection = $objectType->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        // cannot be more precise if final class
        return $classReflection->isFinal();
    }

    private function shouldSkipScope(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        return ! $classReflection->isClass();
    }

    private function shouldSkipReturnExprType(Type $type): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return true;
        }

        $classReflection = $type->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        return $classReflection->isAnonymous();
    }
}

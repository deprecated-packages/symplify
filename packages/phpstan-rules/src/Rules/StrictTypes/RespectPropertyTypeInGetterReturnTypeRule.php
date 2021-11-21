<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\StrictTypes;

use Symplify\PHPStanRules\TypeAnalyzer\PropertyFetchTypeAnalyzer;
use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\TypeAnalyzer\ClassMethodReturnTypeAnalyzer;
use Symplify\PHPStanRules\TypeResolver\NativePropertyFetchTypeResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\RespectPropertyTypeInGetterReturnTypeRuleTest
 */
final class RespectPropertyTypeInGetterReturnTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'This getter method does not respect property type';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NativePropertyFetchTypeResolver $nativePropertyFetchTypeResolver,
        private PropertyFetchTypeAnalyzer $propertyFetchTypeAnalyzer,
        private ClassMethodReturnTypeAnalyzer $classMethodReturnTypeAnalyzer,
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [InClassMethodNode::class];
    }

    /**
     * @param InClassMethodNode $node
     * @return mixed[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classMethod = $node->getOriginalNode();
        if ($classMethod->stmts === null) {
            return [];
        }

        if (\count((array) $classMethod->stmts) !== 1) {
            return [];
        }

        $onlyStmt = $classMethod->stmts[0];
        if (! $onlyStmt instanceof Return_) {
            return [];
        }

        $classMethodReturnType = $this->classMethodReturnTypeAnalyzer->resolve($classMethod, $scope);
        if (!$classMethodReturnType instanceof Type) {
            return [];
        }

        $propertyFetch = $onlyStmt->expr;
        if (! $propertyFetch instanceof PropertyFetch) {
            return [];
        }

        $propertyFetchName = $this->simpleNameResolver->getName($propertyFetch->name);
        if ($propertyFetchName === null) {
            return [];
        }

        $propertyType = $this->nativePropertyFetchTypeResolver->resolve($propertyFetch, $scope);
        if (!$propertyType instanceof Type) {
            return [];
        }

        if ($propertyType->isSuperTypeOf($classMethodReturnType)->yes()) {
            return [];
        }

        // is type one of assigned? Its correct
        $assignedTypes = $this->propertyFetchTypeAnalyzer->resolveAssignedTypes(
            $propertyFetch,
            $propertyFetchName,
            $scope
        );

        foreach ($assignedTypes as $assignedType) {
            if ($classMethodReturnType->accepts($assignedType, false)->yes()) {
                return [];
            }
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $value = [];

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    public function getValues(): array|null
    {
        return $this->values;
    }
}
CODE_SAMPLE
            ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private $value = [];

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}

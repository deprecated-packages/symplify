<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\StrictTypes;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\TypeAnalyzer\ClassMethodReturnTypeResolver;
use Symplify\PHPStanRules\TypeAnalyzer\PropertyFetchTypeAnalyzer;
use Symplify\PHPStanRules\TypeResolver\NativePropertyFetchTypeResolver;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\RespectPropertyTypeInGetterReturnTypeRuleTest
 * @implements Rule<InClassNode>
 */
final class RespectPropertyTypeInGetterReturnTypeRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'This getter method does not respect property type';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NativePropertyFetchTypeResolver $nativePropertyFetchTypeResolver,
        private PropertyFetchTypeAnalyzer $propertyFetchTypeAnalyzer,
        private ClassMethodReturnTypeResolver $classMethodReturnTypeResolver,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $errorMessages = [];

        foreach ($classLike->getMethods() as $classMethod) {
            $propertyFetch = $this->matchOnlyStmtReturnPropertyFetch($classMethod);
            if (! $propertyFetch instanceof PropertyFetch) {
                continue;
            }

            $propertyFetchName = $this->simpleNameResolver->getName($propertyFetch->name);
            if ($propertyFetchName === null) {
                continue;
            }

            $propertyType = $this->nativePropertyFetchTypeResolver->resolve($propertyFetch, $scope);
            if (! $propertyType instanceof Type) {
                continue;
            }

            $classMethodReturnType = $this->classMethodReturnTypeResolver->resolve($classMethod, $scope);
            if ($propertyType->isSuperTypeOf($classMethodReturnType)->yes()) {
                continue;
            }

            // is type one of assigned? Its correct
            if ($this->isOneofAssignedTypes($propertyFetch, $scope, $classLike, $classMethodReturnType)) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($classMethod->getLine())
                ->build();
        }

        return $errorMessages;
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

    private function matchOnlyStmtReturnPropertyFetch(ClassMethod $classMethod): PropertyFetch|null
    {
        if ($classMethod->stmts === null) {
            return null;
        }

        if (\count((array) $classMethod->stmts) !== 1) {
            return null;
        }

        $onlyStmt = $classMethod->stmts[0];
        if (! $onlyStmt instanceof Return_) {
            return null;
        }

        if (! $onlyStmt->expr instanceof PropertyFetch) {
            return null;
        }

        return $onlyStmt->expr;
    }

    private function isOneofAssignedTypes(
        PropertyFetch $propertyFetch,
        Scope $scope,
        Class_ $classLike,
        Type $classMethodReturnType
    ): bool {
        $propertyFetchName = $this->simpleNameResolver->getName($propertyFetch->name);

        $assignedTypes = $this->propertyFetchTypeAnalyzer->resolveAssignedTypes(
            $propertyFetchName,
            $scope,
            $classLike
        );

        foreach ($assignedTypes as $assignedType) {
            if ($classMethodReturnType->accepts($assignedType, false)->yes()) {
                return true;
            }
        }

        return false;
    }
}

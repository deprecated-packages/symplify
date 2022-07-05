<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ThisType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;

/**
 * @implements Collector<MethodCall, array<string>|null>
 */
final class MethodCallCollector implements Collector
{
    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if ($node->name instanceof Expr) {
            return null;
        }

        $callerType = $scope->getType($node->var);

        // remove optional nullable type
        if (TypeCombinator::containsNull($callerType)) {
            $callerType = TypeCombinator::removeNull($callerType);
        }

        // skip self calls, as external is needed to make the method public
        if ($callerType instanceof ThisType) {
            return null;
        }

        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        // move to the class where method is defined, e.g. parent class defines the method, so it should be checked there
        $className = $callerType->getClassName();
        $methodName = $node->name->toString();

        $classMethodReferences = $this->findParentClassMethodReferences($className, $methodName);

        $classMethodReferences[] = $className . '::' . $methodName;

        return $classMethodReferences;
    }

    /**
     * @return string[]
     */
    private function findParentClassMethodReferences(string $className, string $methodName): array
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        $classMethodReferences = [];
        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasNativeMethod($methodName)) {
                $classMethodReferences[] = $parentClassReflection->getName() . '::' . $methodName;
            }
        }

        return $classMethodReferences;
    }
}

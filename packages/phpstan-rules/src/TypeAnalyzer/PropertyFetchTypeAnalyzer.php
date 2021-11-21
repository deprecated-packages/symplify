<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;

final class PropertyFetchTypeAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder,
    ) {
    }

    /**
     * @return Type[]
     */
    public function resolveAssignedTypes(PropertyFetch $propertyFetch, string $propertyFetchName, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $class = $this->simpleNodeFinder->findFirstParentByType($propertyFetch, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        /** @var Assign[] $assigns */
        $assigns = $this->simpleNodeFinder->findByType($class, Assign::class);

        $assignedTypes = [];
        foreach ($assigns as $assign) {
            // assign to property fetch?
            if (! $assign->var instanceof PropertyFetch) {
                continue;
            }

            /** @var PropertyFetch $assignedPropertyFetch */
            $assignedPropertyFetch = $assign->var;

            $assignedPropertyFetchName = $this->simpleNameResolver->getName($assignedPropertyFetch->name);
            if ($assignedPropertyFetchName !== $propertyFetchName) {
                continue;
            }

            // scope does not work here as different class method :/

            $assignedClassMethod = $this->simpleNodeFinder->findFirstParentByType($assign, ClassMethod::class);
            if (! $assignedClassMethod instanceof ClassMethod) {
                return [];
            }

            $assignMethodName = $this->simpleNameResolver->getName($assignedClassMethod);
            if ($assignMethodName === null) {
                return [];
            }

            $assignMethodReflection = $classReflection->getMethod($assignMethodName, $scope);
            foreach ($assignedClassMethod->params as $param) {
                if (! $this->simpleNameResolver->isName($param->var, $propertyFetchName)) {
                    continue;
                }

                // use parameter reflection
                $parametersAcceptor = $assignMethodReflection->getVariants()[0];
                foreach ($parametersAcceptor->getParameters() as $parameterReflection) {
                    if (! $parameterReflection instanceof PhpParameterReflection) {
                        continue;
                    }

                    $assignedTypes[] = $parameterReflection->getType();
                }
            }
        }

        return $assignedTypes;
    }
}

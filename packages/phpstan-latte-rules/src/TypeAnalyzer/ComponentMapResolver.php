<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\TypeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\PHPStanLatteRules\NodeAnalyzer\ComponentClassMethodTypeAnalyzer;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class ComponentMapResolver
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ComponentClassMethodTypeAnalyzer $componentClassMethodTypeAnalyzer,
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    /**
     * @return ComponentNameAndType[]
     */
    public function resolveFromMethodCall(MethodCall $methodCall, Scope $scope): array
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($methodCall, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        return $this->resolveComponentNamesAndTypes($class, $scope);
    }

    /**
     * @return ComponentNameAndType[]
     */
    public function resolveFromClassMethod(ClassMethod $classMethod, Scope $scope): array
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($classMethod, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        return $this->resolveComponentNamesAndTypes($class, $scope);
    }

    /**
     * @return ComponentNameAndType[]
     */
    public function resolveComponentNamesAndTypes(Class_ $class, Scope $scope): array
    {
        $componentNamesAndTypes = [];

        foreach ($class->getMethods() as $classMethod) {
            if (! $this->simpleNameResolver->isName($classMethod, 'createComponent*')) {
                continue;
            }

            /** @var string $methodName */
            $methodName = $this->simpleNameResolver->getName($classMethod);

            $componentName = Strings::after($methodName, 'createComponent');
            if ($componentName === null) {
                throw new ShouldNotHappenException();
            }

            $componentName = lcfirst($componentName);

            $classMethodReturnType = $this->componentClassMethodTypeAnalyzer->resolveReturnType($classMethod, $scope);
            $componentNamesAndTypes[] = new ComponentNameAndType($componentName, $classMethodReturnType);
        }

        return $componentNamesAndTypes;
    }
}

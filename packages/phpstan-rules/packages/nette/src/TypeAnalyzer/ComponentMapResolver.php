<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\TypeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\TypeAnalyzer\ClassMethodTypeAnalyzer;
use Symplify\PHPStanRules\ValueObject\ComponentNameAndType;

final class ComponentMapResolver
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ClassMethodTypeAnalyzer $classMethodTypeAnalyzer,
    ) {
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

            $classMethodReturnType = $this->classMethodTypeAnalyzer->resolveReturnType($classMethod, $scope);
            $componentNamesAndTypes[] = new ComponentNameAndType($componentName, $classMethodReturnType);
        }

        return $componentNamesAndTypes;
    }
}

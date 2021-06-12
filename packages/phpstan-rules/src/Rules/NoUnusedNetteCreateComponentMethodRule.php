<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Nette\LatteUsedControlResolver;
use Symplify\PHPStanRules\NodeAnalyzer\Nette\UsedLocaComponentNamesResolver;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoUnusedNetteCreateComponentMethodRule\NoUnusedNetteCreateComponentMethodRuleTest
 * @implements Rule<ClassMethod>
 */
final class NoUnusedNetteCreateComponentMethodRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The component factory method "%s()" is never used in presenter templates';

    /**
     * @var string
     */
    private const CREATE_COMPONENT = 'createComponent';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private UsedLocaComponentNamesResolver $usedLocaComponentNamesResolver,
        private LatteUsedControlResolver $latteUsedControlResolver
    ) {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($scope, $node)) {
            return [];
        }

        $controlName = $this->resolveControlName($node);
        if ($controlName === null) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $localUsedControlMethodNames = $this->usedLocaComponentNamesResolver->resolveFromClassMethod($node);
        if (in_array($controlName, $localUsedControlMethodNames, true)) {
            return [];
        }

        if ($classReflection->isAbstract()) {
            $layoutUsedControlNames = $this->latteUsedControlResolver->resolveLayoutControlNames();
            if (in_array($controlName, $layoutUsedControlNames, true)) {
                return [];
            }
        }

        $latteUsedControlNames = $this->latteUsedControlResolver->resolveControlNames($scope);
        if (in_array($controlName, $latteUsedControlNames, true)) {
            return [];
        }

        $methodName = $this->simpleNameResolver->getName($node);
        return [sprintf(self::ERROR_MESSAGE, $methodName)];
    }

    private function resolveControlName(ClassMethod $classMethod): ?string
    {
        $classMethodName = $this->simpleNameResolver->getName($classMethod->name);
        if ($classMethodName === null) {
            return null;
        }

        if (! Strings::startsWith($classMethodName, self::CREATE_COMPONENT)) {
            return null;
        }

        $controlName = (string) Strings::after($classMethodName, self::CREATE_COMPONENT);
        return \lcfirst($controlName);
    }

    private function shouldSkip(Scope $scope, ClassMethod $classMethod): bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        if (! $classReflection->isSubclassOf(Presenter::class)) {
            return true;
        }

        return $classMethod->isPrivate();
    }
}

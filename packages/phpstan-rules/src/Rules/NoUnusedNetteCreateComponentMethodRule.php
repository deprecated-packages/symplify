<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

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

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var UsedLocaComponentNamesResolver
     */
    private $usedLocaComponentNamesResolver;

    /**
     * @var LatteUsedControlResolver
     */
    private $latteUsedControlResolver;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        UsedLocaComponentNamesResolver $usedLocaComponentNamesResolver,
        LatteUsedControlResolver $latteUsedControlResolver
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->usedLocaComponentNamesResolver = $usedLocaComponentNamesResolver;
        $this->latteUsedControlResolver = $latteUsedControlResolver;
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

        if (! is_a($className, 'Nette\Application\UI\Presenter', true)) {
            return true;
        }

        return $classMethod->isPrivate();
    }
}

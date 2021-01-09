<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use ReflectionMethod;
use Symplify\PHPStanRules\Exception\NotImplementedException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventParentMethodVisibilityOverrideRule\PreventParentMethodVisibilityOverrideRuleTest
 */
final class PreventParentMethodVisibilityOverrideRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Change "%s()" method visibility to "%s" to respect parent method visibility.';

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
        if ($scope->getClassReflection() === null) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        $parentClassNames = $classReflection->getParentClassesNames();
        if ($parentClassNames === []) {
            return [];
        }

        $methodName = (string) $node->name;
        foreach ($parentClassNames as $parentClassName) {
            if (! method_exists($parentClassName, $methodName)) {
                continue;
            }

            $parentReflectionMethod = new ReflectionMethod($parentClassName, $methodName);
            if ($this->isClassMethodCompatibleWithParentReflectionMethod($node, $parentReflectionMethod)) {
                return [];
            }

            $methodVisibility = $this->resolveReflectionMethodVisibilityAsStrings($parentReflectionMethod);

            $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName, $methodVisibility);
            return [$errorMessage];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass
{
    protected function run()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass
{
    public function run()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isClassMethodCompatibleWithParentReflectionMethod(
        ClassMethod $classMethod,
        ReflectionMethod $reflectionMethod
    ): bool {
        if ($reflectionMethod->isPublic() && $classMethod->isPublic()) {
            return true;
        }

        if ($reflectionMethod->isProtected() && $classMethod->isProtected()) {
            return true;
        }
        if (! $reflectionMethod->isPrivate()) {
            return false;
        }
        return $classMethod->isPrivate();
    }

    private function resolveReflectionMethodVisibilityAsStrings(ReflectionMethod $reflectionMethod): string
    {
        if ($reflectionMethod->isPublic()) {
            return 'public';
        }

        if ($reflectionMethod->isProtected()) {
            return 'protected';
        }

        if ($reflectionMethod->isPrivate()) {
            return 'private';
        }

        throw new NotImplementedException();
    }
}

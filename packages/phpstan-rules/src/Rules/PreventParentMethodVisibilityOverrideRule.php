<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventParentMethodVisibilityOverrideRule\PreventParentMethodVisibilityOverrideRuleTest
 */
final class PreventParentMethodVisibilityOverrideRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Change "%s()" method visibility to "%s" to respect parent method visibility.';

    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
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
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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
            if (! $this->reflectionProvider->hasClass($parentClassName)) {
                continue;
            }

            $parentClassReflection = $this->reflectionProvider->getClass($parentClassName);

            if (! $parentClassReflection->hasMethod($methodName)) {
                continue;
            }

            $parentReflectionMethod = $parentClassReflection->getMethod($methodName, $scope);
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
        MethodReflection $methodReflection
    ): bool {
        if ($methodReflection->isPublic() && $classMethod->isPublic()) {
            return true;
        }

        // see https://github.com/phpstan/phpstan/discussions/7456#discussioncomment-2927978
        $isProtectedMethod = ! $methodReflection->isPublic() && ! $methodReflection->isPrivate();
        if ($isProtectedMethod && $classMethod->isProtected()) {
            return true;
        }

        if (! $methodReflection->isPrivate()) {
            return false;
        }

        return $classMethod->isPrivate();
    }

    private function resolveReflectionMethodVisibilityAsStrings(MethodReflection $reflectionMethod): string
    {
        if ($reflectionMethod->isPublic()) {
            return 'public';
        }

        if ($reflectionMethod->isPrivate()) {
            return 'private';
        }

        return 'protected';
    }
}

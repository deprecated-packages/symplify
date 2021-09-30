<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDefaultParameterValueRule\NoDefaultParameterValueRuleTest
 */
final class NoDefaultParameterValueRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter "%s" cannot have default value';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
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
        if ($this->isParentContractClassMethod($node, $scope)) {
            return [];
        }

        $errorMessages = [];
        foreach ($node->params as $param) {
            if ($param->default === null) {
                continue;
            }

            /** @var string $paramName */
            $paramName = $this->simpleNameResolver->getName($param);
            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $paramName);
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run($value = true): void
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run($value): void
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isParentContractClassMethod(ClassMethod $classMethod, Scope $scope): bool
    {
        /** @var string $classMethodName */
        $classMethodName = $this->simpleNameResolver->getName($classMethod);

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getAncestors() as $ancestorClassReflection) {
            // skip itself
            if ($classReflection === $ancestorClassReflection) {
                continue;
            }

            if ($ancestorClassReflection->hasMethod($classMethodName)) {
                return true;
            }
        }

        return false;
    }
}

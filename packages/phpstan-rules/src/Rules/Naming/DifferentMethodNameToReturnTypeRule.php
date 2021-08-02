<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Naming;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Naming\DifferentMethodNameToReturnTypeRule\DifferentMethodNameToReturnTypeRuleTest
 */
final class DifferentMethodNameToReturnTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method name should be different to its return type, in a verb form';

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
        $shortReturnTypeName = $this->resolveShortReturnType($node);
        if ($shortReturnTypeName === null) {
            return [];
        }

        /** @var string $methodName */
        $methodName = $this->simpleNameResolver->getName($node->name);

        if (strtolower($methodName) !== strtolower($shortReturnTypeName)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function apple(): Apple
    {
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function getApple(): Apple
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function resolveShortReturnType(ClassMethod $classMethod): ?string
    {
        $returnType = $classMethod->getReturnType();
        if (! $returnType instanceof Name) {
            return null;
        }

        $returnTypeName = $this->simpleNameResolver->getName($returnType);
        if ($returnTypeName === null) {
            return null;
        }

        if (str_contains($returnTypeName, '\\')) {
            return Strings::after($returnTypeName, '\\', -1);
        }

        return $returnTypeName;
    }
}

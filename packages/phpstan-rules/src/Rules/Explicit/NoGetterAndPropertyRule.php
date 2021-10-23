<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoGetterAndPropertyRule\NoGetterAndPropertyRuleTest
 */
final class NoGetterAndPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There are 2 way to get "%s" value: public property and getter now - pick one to avoid variant behavior.';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeProduct
{
    public $name;

    public function getName(): string
    {
        return $this->name;
    }
}
CODE_SAMPLE
             ,
                <<<'CODE_SAMPLE'
final class SomeProduct
{
    private $name;

    public function getName(): string
    {
        return $this->name;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [InClassNode::class];
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();

        $errors = [];

        $publicPropertyNames = $this->resolvePublicPropertyNames($classLike);
        $publicMethodNames = $this->resolvePublicMethodNames($classLike);

        foreach ($publicPropertyNames as $publicPropertyName) {
            $getterMethodName = 'get' . ucfirst($publicPropertyName);
            $isserMethodName = 'is' . ucfirst($publicPropertyName);

            if (! array_intersect([$getterMethodName, $isserMethodName], $publicMethodNames)) {
                continue;
            }

            $errors[] = sprintf(self::ERROR_MESSAGE, $publicPropertyName);
        }

        return $errors;
    }

    /**
     * @return string[]
     */
    private function resolvePublicPropertyNames(ClassLike $classLike): array
    {
        $propertyNames = [];
        foreach ($classLike->getProperties() as $property) {
            if (! $property->isPublic()) {
                continue;
            }

            $propertyName = $this->simpleNameResolver->getName($property);
            if ($propertyName === null) {
                continue;
            }

            $propertyNames[] = $propertyName;
        }

        return $propertyNames;
    }

    /**
     * @return string[]
     */
    private function resolvePublicMethodNames(ClassLike $classLike): array
    {
        $methodNames = [];
        foreach ($classLike->getMethods() as $classMethod) {
            if (! $classMethod->isPublic()) {
                continue;
            }

            $methodName = $this->simpleNameResolver->getName($classMethod);
            if ($methodName === null) {
                continue;
            }

            $methodNames[] = $methodName;
        }

        return $methodNames;
    }
}

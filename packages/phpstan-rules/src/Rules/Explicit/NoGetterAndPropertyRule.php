<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoGetterAndPropertyRule\NoGetterAndPropertyRuleTest
 */
final class NoGetterAndPropertyRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There are 2 way to get "%s" value: public property and getter now - pick one to avoid variant behavior.';

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
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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

            $propertyProperty = $property->props[0];
            $propertyNames[] = $propertyProperty->name->toString();
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

            $methodNames[] = $classMethod->name->toString();
        }

        return $methodNames;
    }
}

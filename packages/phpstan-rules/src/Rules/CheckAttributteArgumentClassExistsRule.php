<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\CheckAttributteArgumentClassExistsRuleTest
 */
final class CheckAttributteArgumentClassExistsRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class was not found';

    /**
     * @param array<string, string[]> $argumentsByAttributes
     */
    public function __construct(
        private array $argumentsByAttributes,
        private SimpleNameResolver $simpleNameResolver,
        private NodeValueResolver $nodeValueResolver,
        private ReflectionProvider $reflectionProvider,
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, Property::class, ClassMethod::class];
    }

    /**
     * @param Class_|Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Attribute[] $attributes */
        $attributes = $this->nodeFinder->findInstanceOf($node, Attribute::class);

        foreach ($attributes as $attribute) {
            foreach ($this->argumentsByAttributes as $attributeName => $argumentNames) {
                if (! $this->simpleNameResolver->isName($attribute->name, $attributeName)) {
                    continue;
                }

                if (! $this->hasMatchingArgumentWithMissingClassName($attribute, $argumentNames, $scope)) {
                    continue;
                }

                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
#[SomeAttribute(firstName: 'MissingClass::class')]
class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
#[SomeAttribute(firstName: ExistingClass::class)]
class SomeClass
{
}
CODE_SAMPLE
                ,
                [
                    '$argumentsByAttributes' => [
                        'SomeAttribute' => ['firstName'],
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param string[] $argumentNames
     */
    private function isArgumentNameMatch(Arg $arg, array $argumentNames): bool
    {
        if ($arg->name === null) {
            return false;
        }

        $argumentArgName = $this->simpleNameResolver->getName($arg->name);
        return in_array($argumentArgName, $argumentNames, true);
    }

    /**
     * @param string[] $argumentNames
     */
    private function hasMatchingArgumentWithMissingClassName(
        Attribute $attribute,
        array $argumentNames,
        Scope $scope
    ): bool {
        foreach ($attribute->args as $arg) {
            if (! $this->isArgumentNameMatch($arg, $argumentNames)) {
                return false;
            }

            $currentArgumentName = $this->nodeValueResolver->resolve($arg->value, $scope->getFile());
            return ! $this->reflectionProvider->hasClass($currentArgumentName);
        }

        return false;
    }
}

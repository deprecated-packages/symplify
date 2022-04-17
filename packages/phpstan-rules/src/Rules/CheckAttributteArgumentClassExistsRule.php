<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule\CheckAttributteArgumentClassExistsRuleTest
 */
final class CheckAttributteArgumentClassExistsRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class was not found';

    public function __construct(
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
            foreach ($attribute->args as $arg) {
                $value = $arg->value;
                if (! $this->isClassConstFetch($value)) {
                    continue;
                }

                $classConstValue = $this->nodeValueResolver->resolve($value, $scope->getFile());
                if ($classConstValue === null) {
                    return [self::ERROR_MESSAGE];
                }

                if ($this->reflectionProvider->hasClass($classConstValue)) {
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
            new CodeSample(
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
            ),
        ]);
    }

    private function isClassConstFetch(Expr $expr): bool
    {
        if (! $expr instanceof ClassConstFetch) {
            return false;
        }

        return $this->simpleNameResolver->isName($expr->name, 'class');
    }
}

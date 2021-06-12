<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\CheckUsedNamespacedNameOnClassNodeRuleTest
 */
final class CheckUsedNamespacedNameOnClassNodeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use `$class->namespaceName` instead of `$class->name` that only returns short class name';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @param PropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $type = $scope->getType($node->var);
        if (! $type instanceof TypeWithClassName) {
            return [];
        }

        if ($type->getClassName() !== Class_::class) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->name, 'name')) {
            return [];
        }

        if ($this->shouldSkip($node)) {
            return [];
        }

        if ($this->isVariableNamedShortClassName($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class)
    {
        $className = (string) $class->name;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class)
    {
        $className = (string) $class->namespacedName;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isVariableNamedShortClassName(PropertyFetch $propertyFetch): bool
    {
        /** @var Assign|null $assign */
        $assign = $this->simpleNodeFinder->findFirstParentByType($propertyFetch, Assign::class);
        if (! $assign instanceof Assign) {
            return false;
        }

        /** @var Variable $classNameVariable */
        $classNameVariable = $assign->var;

        return $this->simpleNameResolver->isName($classNameVariable->name, 'shortClassName');
    }

    private function shouldSkip(PropertyFetch $propertyFetch): bool
    {
        $parent = $propertyFetch->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof BinaryOp) {
            return true;
        }
        if (! $parent instanceof Assign) {
            return false;
        }
        return $parent->var === $propertyFetch;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenForeachEmptyMissingArrayRule\ForbiddenForeachEmptyMissingArrayRuleTest
 */
final class ForbiddenForeachEmptyMissingArrayRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Foreach over empty missing array is not allowed. Use isset check early instead.';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Foreach_::class];
    }

    /**
     * @param Node\Stmt\Foreach_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof  Coalesce) {
            return [];
        }

        if (! $node->expr->right instanceof Array_) {
            return [];
        }

        if ($node->expr->right->items !== []) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(): void
    {
        foreach ($data ?? [] as $value) {
            // ...
        }
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(): void
    {
        if (! isset($data)) {
            return;
        }

        foreach ($data as $value) {
            // ...
        }
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}

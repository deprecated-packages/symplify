<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoBinaryOpCallCompareRule\NoBinaryOpCallCompareRuleTest
 */
final class NoBinaryOpCallCompareRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not compare call directly, use a variable assign';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Identical::class, NotIdentical::class];
    }

    /**
     * @param BinaryOp $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->left instanceof MethodCall && $node->right instanceof MethodCall) {
            return [];
        }

        if ($this->isForbiddenCall($node->left)) {
            return [self::ERROR_MESSAGE];
        }

        if ($this->isForbiddenCall($node->right)) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'No magic closure function call is allowed, use explicit class with method instead ',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
return array_filter($items, function ($item) {
}) !== [];
CODE_SAMPLE
    ,
                    <<<'CODE_SAMPLE'
$values = array_filter($items, function ($item) {
});
return $values !== [];
CODE_SAMPLE
                ),
            ]
        );
    }

    private function isForbiddenCall(Expr $expr): bool
    {
        if ($expr instanceof FuncCall) {
            return ! $this->simpleNameResolver->isNames($expr, [
                'count',
                'trim',
                'getcwd',
                'get_class',
                'array_keys',
                'constant',
                'substr_count',
                'strpos',
                'strlen',
                'strtolower',
                'strtoupper',
                'defined',
            ]);
        }

        return $expr instanceof StaticCall;
    }
}

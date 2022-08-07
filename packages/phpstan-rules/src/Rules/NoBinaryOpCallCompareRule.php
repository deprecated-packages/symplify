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
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoBinaryOpCallCompareRule\NoBinaryOpCallCompareRuleTest
 */
final class NoBinaryOpCallCompareRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not compare call directly, use a variable assign';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return BinaryOp::class;
    }

    /**
     * @param BinaryOp $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof Identical && ! $node instanceof NotIdentical) {
            return [];
        }

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
            if (! $expr->name instanceof Name) {
                return false;
            }

            $functionName = $expr->name->toString();
            return ! in_array($functionName, [
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
            ], true);
        }

        return $expr instanceof StaticCall;
    }
}

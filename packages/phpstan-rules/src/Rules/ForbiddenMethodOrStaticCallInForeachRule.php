<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node\Expr;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\ForbiddenMethodOrStaticCallInForeachRuleTest
 */
final class ForbiddenMethodOrStaticCallInForeachRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method nor static call in foreach is not allowed. Extract expression to a new variable assign on line before';

    /**
     * @var array<class-string<Expr>>
     */
    private const CALL_CLASS_TYPES = [MethodCall::class, StaticCall::class];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Foreach_::class];
    }

    /**
     * @param Foreach_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach (self::CALL_CLASS_TYPES as $expressionClassType) {
            /** @var MethodCall[]|StaticCall[] $calls */
            $calls = $this->nodeFinder->findInstanceOf($node->expr, $expressionClassType);
            if (! $this->hasCallArgs($calls)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
foreach ($this->getData($arg) as $key => $item) {
    // ...
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$data = $this->getData($arg);
foreach ($arg as $key => $item) {
    // ...
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param MethodCall[]|StaticCall[] $calls
     */
    private function hasCallArgs(array $calls): bool
    {
        foreach ($calls as $call) {
            if ($call->args !== []) {
                return true;
            }
        }

        return false;
    }
}

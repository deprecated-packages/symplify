<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PHPStan\Analyser\Scope;
use PHPStan\Type\VoidType;
use Symplify\Astral\Reflection\ReflectionParser;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule\NoVoidAssignRuleTest
 */
final class NoVoidAssignRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assign of void value is not allowed, as it can lead to unexpected results';

    public function __construct(
        private ReflectionParser $reflectionParser
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $value = $this->getNothing();
    }

    public function getNothing(): void
    {
    }
}
CODE_SAMPLE
            ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $this->getNothing();
    }

    public function getNothing(): void
    {
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
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->expr instanceof Node\Expr\MethodCall) {
            // parse method call to
            dump('__DD');
            die;
        }

        $assignedExprType = $scope->getType($node->expr);


        if (! $assignedExprType instanceof VoidType) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

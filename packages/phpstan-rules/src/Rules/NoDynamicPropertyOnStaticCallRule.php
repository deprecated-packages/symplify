<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\NoDynamicPropertyOnStaticCallRuleTest
 */
final class NoDynamicPropertyOnStaticCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use non-dynamic property on static call';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class, ClassConstFetch::class];
    }

    /**
     * @param StaticCall|ClassConstFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->class instanceof Name) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return $this->connection::literal;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return Connection::literal;
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}

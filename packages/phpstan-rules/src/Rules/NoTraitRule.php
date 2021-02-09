<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoTraitRule\NoTraitRuleTest
 */
final class NoTraitRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use trait, extract to a service and dependency injection instead';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Trait_::class];
    }

    /**
     * @param Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
trait SomeTrait
{
    public function run()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeService
{
    public function run(...)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}

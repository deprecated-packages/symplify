<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoAbstractRule\NoAbstractRuleTest
 */
final class NoAbstractRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Variables "%s" are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class NormalHelper extends AbstractHelper
{
}

abstract class AbstractHelper
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class NormalHelper
{
    public function __construct(
        private SpecificHelper $specificHelper
    ) {
    }
}

final class SpecificHelper
{
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
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]|RuleError[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isAbstract()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

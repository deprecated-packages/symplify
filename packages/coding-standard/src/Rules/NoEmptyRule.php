<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Empty_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoEmptyRule\NoEmptyRuleTest
 */
final class NoEmptyRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use strict comparison instead of empty';

    /**
     * @return array<int, string>
     */
    public function getNodeTypes(): array
    {
        return [Empty_::class];
    }

    /**
     * @param Empty_ $node
     * @return array<int, string>
     */
    public function process(Node $node, Scope $scope): array
    {
        return [self::ERROR_MESSAGE];
    }
}

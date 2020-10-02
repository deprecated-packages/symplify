<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Empty_;
use PHPStan\Analyser\Scope;

/**
 * @deprecated Use
 * @see NoParticularNodeRule with configuration instead
 *
 * @see \Symplify\CodingStandard\Tests\Rules\NoEmptyRule\NoEmptyRuleTest
 */
final class NoEmptyRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use strict comparison instead of empty';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Empty_::class];
    }

    /**
     * @param Empty_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        return [self::ERROR_MESSAGE];
    }
}

<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenSpreadOperatorRule\ForbiddenSpreadOperatorRuleTest
 */
final class ForbiddenSpreadOperatorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Spread operator is not allowed.';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Arg::class];
    }

    /**
     * @param Arg $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->unpack) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

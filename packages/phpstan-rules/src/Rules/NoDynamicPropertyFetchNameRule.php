<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyFetchNameRule\NoDynamicPropertyFetchNameRuleTest
 */
final class NoDynamicPropertyFetchNameRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit property fetch names over dynamic';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class, StaticPropertyFetch::class];
    }

    /**
     * @param PropertyFetch|StaticPropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Expr) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

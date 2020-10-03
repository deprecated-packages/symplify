<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\PreferredClassConstantOverVariableConstantRuleTest
 */
final class PreferredClassConstantOverVariableConstantRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class constant is preferred over Variable constant';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class];
    }

    /**
     * @param ClassConstFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Variable) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

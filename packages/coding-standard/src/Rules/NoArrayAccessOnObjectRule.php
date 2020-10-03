<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use SplFixedArray;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoArrayAccessOnObjectRule\NoArrayAccessOnObjectRuleTest
 */
final class NoArrayAccessOnObjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit methods, over array acccess on object';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $varStaticType = $scope->getType($node->var);
        if (! $varStaticType instanceof TypeWithClassName) {
            return [];
        }

        if (is_a($varStaticType->getClassName(), SplFixedArray::class, true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

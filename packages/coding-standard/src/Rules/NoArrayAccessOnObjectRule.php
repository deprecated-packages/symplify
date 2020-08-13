<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\TypeWithClassName;
use SplFixedArray;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoArrayAccessOnObjectRule\NoArrayAccessOnObjectRuleTest
 */
final class NoArrayAccessOnObjectRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit methods, over array acccess on object';

    public function getNodeType(): string
    {
        return ArrayDimFetch::class;
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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

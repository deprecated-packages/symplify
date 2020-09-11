<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoArrayStringObjectReturnRule\NoArrayStringObjectReturnRuleTest
 */
final class NoArrayStringObjectReturnRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use another value object over string with value object arrays';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $methodCallReturnType = $scope->getType($node);
        if (! $methodCallReturnType instanceof ArrayType) {
            return [];
        }

        if (! $methodCallReturnType->getKeyType() instanceof StringType) {
            return [];
        }

        if (! $methodCallReturnType->getItemType() instanceof ObjectType) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#5-use-only-one-object-operator---per-statement
 *
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\NoChainMethodCallRuleTest
 */
final class NoChainMethodCallRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use chained method calls';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof MethodCall) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

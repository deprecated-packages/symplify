<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule\PreferredClassConstantOverVariableConstantRuleTest
 */
final class PreferredClassConstantOverVariableConstantRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class constant is preferred over Variable constant';

    public function getNodeType(): string
    {
        return ClassConstFetch::class;
    }

    /**
     * @param ClassConstFetch $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Variable) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

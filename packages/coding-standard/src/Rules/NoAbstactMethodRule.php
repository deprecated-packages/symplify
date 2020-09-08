<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoAbstractMethodRule\NoAbstractMethodRuleTest
 */
final class NoAbstactMethodRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit interface contract or a service over unclear abstract methods';

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isAbstract()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }
}

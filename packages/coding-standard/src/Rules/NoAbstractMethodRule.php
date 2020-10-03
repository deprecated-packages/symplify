<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoAbstractMethodRule\NoAbstractMethodRuleTest
 */
final class NoAbstractMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit interface contract or a service over unclear abstract methods';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isAbstract()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\ForbiddenProtectedPropertyRuleTest
 */
final class ForbiddenProtectedPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property with protected modifier is not allowed. Use interface instead.';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassConst::class];
    }

    /**
     * @param Property $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isProtected()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

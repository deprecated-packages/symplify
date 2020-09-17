<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\NoProtectedElementInFinalClassRuleTest
 */
final class NoProtectedElementInFinalClassRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use protected element in final class';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class];
    }

    /**
     * @param Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $parent = $node->getAttribute('parent');
        if (! $parent instanceof Class_) {
            return [];
        }

        if (! $parent->isFinal()) {
            return [];
        }

        if ($parent->extends !== null) {
            return [];
        }

        if (! $node->isProtected()) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

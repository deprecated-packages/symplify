<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoTraitExceptItsMethodsRequired\NoTraitExceptItsMethodsRequiredTest
 */
final class NoTraitExceptItsMethodsRequired extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use trait';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Identifier $name */
        $name = $node->namespacedName;
        $className = $name->toString();
        $usedTraits = class_uses($className);
        if ($usedTraits === []) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}

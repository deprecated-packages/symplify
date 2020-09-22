<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoTraitExceptItsMethodsPublicAndRequired\NoTraitExceptItsMethodsPublicAndRequiredTest
 */
final class NoTraitExceptItsMethodsPublicAndRequired extends AbstractManyNodeTypeRule
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
        return [Trait_::class];
    }

    /**
     * @param Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $methods = $node->getMethods();
        foreach ($methods as $method) {
            if ($method->isPublic()) {
                return [];
            }
        }

        return [self::ERROR_MESSAGE];
    }
}

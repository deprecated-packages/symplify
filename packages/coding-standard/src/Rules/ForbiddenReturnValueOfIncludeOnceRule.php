<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenReturnValueOfIncludeOnceRule\ForbiddenReturnValueOfIncludeOnceRuleTest
 */
final class ForbiddenReturnValueOfIncludeOnceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Cannot return include_once/require_once';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Node::class];
    }

    /**
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node instanceof Assign && ! $node instanceof Return_) {
            return [];
        }

        if (! $this->isIncludeOnceOrRequireOnce($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @param Assign|Return_ $node
     */
    private function isIncludeOnceOrRequireOnce(Node $node): bool
    {
        if (! $node->expr instanceof Include_) {
            return false;
        }

        return in_array($node->expr->type, [Include_::TYPE_REQUIRE_ONCE, Include_::TYPE_REQUIRE_ONCE], true);
    }
}

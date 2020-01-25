<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Rules\Include_;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

final class ForbidReturnValueOfIncludeOnceRule implements Rule
{
    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof Assign && ! $node instanceof Return_) {
            return [];
        }

        if (! $this->isIncludeOnceOrRequireOnce($node)) {
            return [];
        }

        // is include_once/require_once
        return ['Cannot return include_once/require_once'];
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

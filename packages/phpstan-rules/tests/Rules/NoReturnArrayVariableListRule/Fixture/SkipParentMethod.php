<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Contract\ManyNodeRuleInterface;

final class SkipParentMethod implements ManyNodeRuleInterface
{
    public function getNodeTypes(): array
    {
        return [Array_::class, New_::class];
    }

    public function process(Node $node, Scope $scope): array
    {
    }
}


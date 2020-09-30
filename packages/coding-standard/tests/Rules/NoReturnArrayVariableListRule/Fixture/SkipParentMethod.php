<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReturnArrayVariableListRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\Rules\AbstractManyNodeTypeRule;

final class SkipParentMethod extends AbstractManyNodeTypeRule
{
    public function getNodeTypes(): array
    {
        return [Array_::class, New_::class];
    }

    public function process(Node $node, Scope $scope): array
    {

    }
}


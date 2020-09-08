<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Contract;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

interface ManyNodeRuleInterface
{
    public function getNodeTypes(): array;

    public function process(Node $node, Scope $scope): array;
}

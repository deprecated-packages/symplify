<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Contract;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

interface ManyNodeRuleInterface
{
    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array;

    /**
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array;
}

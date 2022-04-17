<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Contract;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;

interface ManyNodeRuleInterface
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string;

    /**
     * @return array<string|RuleError>
     */
    public function processNode(Node $node, Scope $scope): array;
}

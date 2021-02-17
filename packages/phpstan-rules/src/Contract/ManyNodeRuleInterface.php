<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Contract;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;

interface ManyNodeRuleInterface
{
    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array;

    /**
     * @return array<string|RuleError>
     */
    public function process(Node $node, Scope $scope): array;
}

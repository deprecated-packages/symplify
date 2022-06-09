<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Contract;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;

/**
 * @deprecated Make use of native Rule interfaces, to keep the complexity simple
 */
interface ManyNodeRuleInterface
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array;

    /**
     * @return array<string|RuleError>
     */
    public function process(Node $node, Scope $scope): array;
}

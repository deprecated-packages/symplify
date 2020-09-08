<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Contract\ManyNodeRuleInterface;

abstract class AbstractManyNodeTypeRule implements Rule, ManyNodeRuleInterface
{
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node)) {
            return [];
        }

        return $this->process($node, $scope);
    }

    private function shouldSkip(Node $node): bool
    {
        foreach ($this->getNodeTypes() as $nodeType) {
            if (is_a($node, $nodeType, true)) {
                return false;
            }
        }

        return true;
    }
}

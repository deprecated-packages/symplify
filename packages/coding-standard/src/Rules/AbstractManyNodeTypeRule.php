<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

abstract class AbstractManyNodeTypeRule implements Rule
{
    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node)) {
            return [];
        }

        return $this->process($node, $scope);
    }

    /**
     * @return class-string[]
     */
    abstract public function getNodeTypes(): array;

    abstract public function process(Node $node, Scope $scope): array;

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

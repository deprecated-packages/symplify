<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Contract\ManyNodeRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

/**
 * @template NodeType of Node
 *
 * @implements Rule<NodeType>
 */
abstract class AbstractSymplifyRule implements Rule, ManyNodeRuleInterface, DocumentedRuleInterface
{
    /**
     * @return class-string<NodeType>
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @param NodeType $node
     *
     * @return array<string|RuleError>
     */
    abstract function process(Node $node, Scope $scope): array;

    /**
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipNode($node)) {
            return [];
        }

        return $this->process($node, $scope);
    }

    private function shouldSkipNode(Node $node): bool
    {
        $nodeTypes = $this->getNodeTypes();
        foreach ($nodeTypes as $nodeType) {
            if (is_a($node, $nodeType, true)) {
                return false;
            }
        }

        return true;
    }
}

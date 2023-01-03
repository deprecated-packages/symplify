<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use Symplify\PHPStanRules\Contract\ManyNodeRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

/**
 * @implements Rule<Node>
 */
abstract class AbstractSymplifyRule implements Rule, ManyNodeRuleInterface, DocumentedRuleInterface
{
    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return string[]|RuleError[]
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
            if ($node instanceof $nodeType) {
                return false;
            }
        }

        return true;
    }
}

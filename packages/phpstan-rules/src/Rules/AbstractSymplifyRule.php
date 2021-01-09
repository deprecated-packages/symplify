<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Contract\ManyNodeRuleInterface;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

abstract class AbstractSymplifyRule implements Rule, ManyNodeRuleInterface, DocumentedRuleInterface
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
        if ($this->shouldSkipNode($node)) {
            return [];
        }

        return $this->process($node, $scope);
    }

    public function resolveCurrentClassMethod(Node $node): ?ClassMethod
    {
        return $this->getFirstParentByType($node, ClassMethod::class);
    }

    /**
     * @see https://phpstan.org/blog/generics-in-php-using-phpdocs for template
     *
     * @template T of Node
     * @param class-string<T> $nodeClass
     * @return T|null
     */
    public function getFirstParentByType(Node $node, string $nodeClass): ?Node
    {
        $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        while ($node) {
            if (is_a($node, $nodeClass, true) && $node instanceof Node) {
                return $node;
            }

            $node = $node->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return null;
    }

    protected function resolveCurrentClass(Node $node): ?Class_
    {
        if ($node instanceof Class_) {
            return $node;
        }

        return $this->getFirstParentByType($node, Class_::class);
    }

    protected function isInAbstractClass(Node $node): bool
    {
        $class = $this->resolveCurrentClass($node);
        if ($class === null) {
            return false;
        }

        return $class->isAbstract();
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

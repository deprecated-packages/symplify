<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\ValueObject\AttributeKey;

final class UsedClassNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $usedNames = [];

    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        $this->usedNames = [];
        return $nodes;
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof Name) {
            return null;
        }

        if ($this->isNonNameNode($node)) {
            return null;
        }

        // class names itself are skipped automatically, as they are Identifier node

        $this->usedNames[] = $node->toString();

        return null;
    }

    /**
     * @return string[]
     */
    public function getUsedNames(): array
    {
        $uniqueUsedNames = array_unique($this->usedNames);
        sort($uniqueUsedNames);

        return $uniqueUsedNames;
    }

    private function isNonNameNode(Name $name): bool
    {
        // skip nodes that are not part of class names
        $parent = $name->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof Namespace_) {
            return true;
        }

        return $parent instanceof ClassMethod;
    }
}

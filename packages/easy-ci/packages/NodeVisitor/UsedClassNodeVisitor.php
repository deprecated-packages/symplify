<?php

declare(strict_types=1);

namespace Symplify\EasyCI\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\ValueObject\AttributeKey;

final class UsedClassNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $usedNames = [];

    public function beforeTraverse(array $nodes)
    {
        $this->usedNames = [];
        return $nodes;
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof Name) {
            return null;
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof ClassLike) {
            // skip class name itself
            if ($parent->name === $node) {
                return null;
            }
        }

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
}

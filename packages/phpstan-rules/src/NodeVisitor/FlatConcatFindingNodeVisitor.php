<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\NodeAnalyzer\FileCheckingFuncCallAnalyzer;

final class FlatConcatFindingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var Concat[]
     */
    private array $foundNodes = [];

    public function __construct(
        private FileCheckingFuncCallAnalyzer $fileCheckingFuncCallAnalyzer
    ) {
    }

    /**
     * @param Node[] $nodes
     */
    public function beforeTraverse(array $nodes)
    {
        $this->foundNodes = [];
        return null;
    }

    public function enterNode(Node $node)
    {
        if ($this->fileCheckingFuncCallAnalyzer->isFileExistCheck($node)) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if (! $node instanceof Concat) {
            return null;
        }

        if ($node->left instanceof Concat) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node->right instanceof Concat) {
            return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }

        $this->foundNodes[] = $node;
        return null;
    }

    /**
     * @return Concat[]
     */
    public function getFoundNodes(): array
    {
        return $this->foundNodes;
    }
}

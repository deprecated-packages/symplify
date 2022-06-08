<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class FlatConcatFindingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var Concat[]
     */
    private array $foundNodes = [];

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
        if ($this->isFileCheckingFuncCall($node)) {
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

    private function isFileCheckingFuncCall(Node $node): bool
    {
        if (! $node instanceof If_) {
            return false;
        }

        if (! $node->cond instanceof FuncCall) {
            return false;
        }

        $funcCallCond = $node->cond;
        if (! $funcCallCond->name instanceof Name) {
            return false;
        }

        $funcCallName = $funcCallCond->name->toString();

        return in_array($funcCallName, ['is_file', 'file_exists', 'is_dir'], true);
    }
}

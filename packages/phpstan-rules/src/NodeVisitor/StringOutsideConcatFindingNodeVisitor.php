<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\NodeAnalyzer\FileCheckingFuncCallAnalyzer;

final class StringOutsideConcatFindingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var String_[]
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

        if ($node instanceof Concat) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if (! $node instanceof String_) {
            return null;
        }

        $stringKind = $node->getAttribute('kind');

        // skip here/now docs, not a file
        if (in_array($stringKind, [String_::KIND_HEREDOC, String_::KIND_NOWDOC], true)) {
            return null;
        }

        $this->foundNodes[] = $node;
        return null;
    }

    /**
     * @return String_[]
     */
    public function getFoundNodes(): array
    {
        return $this->foundNodes;
    }
}

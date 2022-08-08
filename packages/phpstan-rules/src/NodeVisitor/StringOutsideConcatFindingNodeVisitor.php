<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\Enum\AttributeKey;
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

        if ($this->isStrFuncCall($node)) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof Concat) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if (! $node instanceof String_) {
            return null;
        }

        $stringKind = $node->getAttribute(AttributeKey::KIND);

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

    private function isStrFuncCall(Node $node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        if (! $node->name instanceof Name) {
            return false;
        }

        $functionName = $node->name->toString();

        return in_array($functionName, ['str_ends_with', 'strpos', 'str_stars_with', 'sprintf'], true);
    }
}

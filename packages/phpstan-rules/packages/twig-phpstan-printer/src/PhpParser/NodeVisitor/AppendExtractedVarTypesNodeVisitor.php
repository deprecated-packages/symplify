<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TwigPHPStanPrinter\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeFactory\VarDocNodeFactory;

final class AppendExtractedVarTypesNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private VarDocNodeFactory $varDocNodeFactory,
        private array $variablesAndTypes
    ) {
    }

    public function enterNode(Node $node): Node|null
    {
        // look for "doDisplay()"
        if (! $node instanceof ClassMethod) {
            return null;
        }

        if (! $this->simpleNameResolver->isName($node, 'doDisplay')) {
            return null;
        }

        $docNodes = $this->varDocNodeFactory->createDocNodes($this->variablesAndTypes);

        // needed to ping phpstan about possible invisbile variables
        $extractFuncCall = new Node\Expr\FuncCall(new Node\Name('extract'));
        $extractFuncCall->args[] = new Node\Arg(new Node\Expr\Variable('context'));
        $funcCallExpression = new Node\Stmt\Expression($extractFuncCall);

        $node->stmts = array_merge([$funcCallExpression], $docNodes, $node->stmts);
        return $node;
    }
}

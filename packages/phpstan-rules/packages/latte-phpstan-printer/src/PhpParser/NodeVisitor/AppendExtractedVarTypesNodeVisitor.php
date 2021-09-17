<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\VariableAndType;

final class AppendExtractedVarTypesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private array $variablesAndTypes
    ) {
    }

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        // nothing to wrap
        if ($node->stmts === null) {
            return null;
        }

        foreach ($node->stmts as $key => $classMethodStmt) {
            if (! $classMethodStmt instanceof Expression) {
                continue;
            }

            $extractMethodCall = $classMethodStmt->expr;

            if (! $extractMethodCall instanceof FuncCall) {
                continue;
            }

            if (! $this->simpleNameResolver->isName($extractMethodCall, 'extract')) {
                continue;
            }

            $docNodes = $this->createDocNodes();

            // must be AFTER extract(), otherwise the variable does not exists
            array_splice($node->stmts, $key + 1, 0, $docNodes);

            return $node;
        }

        return null;
    }

    /**
     * @return Nop[]
     */
    private function createDocNodes(): array
    {
        $docNodes = [];
        foreach ($this->variablesAndTypes as $variableAndType) {
            $docNodes[] = $this->createDocNop($variableAndType);
        }

        return $docNodes;
    }

    private function createDocNop(VariableAndType $variableAndType): Nop
    {
        $prependVarTypesDocBlocks = sprintf(
            '/** @var %s $%s */',
            $variableAndType->getTypeAsString(),
            $variableAndType->getVariable()
        );

        // doc types node
        $docNop = new Nop();
        $docNop->setDocComment(new Doc($prependVarTypesDocBlocks));

        return $docNop;
    }
}

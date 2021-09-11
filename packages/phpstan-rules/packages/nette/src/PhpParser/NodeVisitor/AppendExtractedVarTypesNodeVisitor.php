<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndType;

final class AppendExtractedVarTypesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    public function __construct(
        private array $variablesAndTypes
    ) {
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        if ($node->name->toString() !== 'main') {
            return null;
        }

        // nothing to wrap
        if ($node->stmts === null) {
            return null;
        }

        $prependVarTypesDocBlocks = '';
        foreach ($this->variablesAndTypes as $variableAndType) {
            $prependVarTypesDocBlocks .= sprintf(
                '/** @var %s $%s */',
                $variableAndType->getTypeAsString(),
                $variableAndType->getVariable()
            );
        }

        // doc types node
        $docNop = new Nop();
        $docNop->setDocComment(new Doc($prependVarTypesDocBlocks));

        // must be AFTER extract(), otherwise the variable does not exists
        $firstNode = array_shift($node->stmts);

        /** @var Node\Stmt[] $classMethodStmts */
        $classMethodStmts = array_merge([$firstNode], [$docNop], $node->stmts);

        $node->stmts = $classMethodStmts;
        return $node;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
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
        if (! $node instanceof Node\Stmt\ClassMethod) {
            return null;
        }

        if ($node->name->toString() !== 'main') {
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
        $docNop = new Node\Stmt\Nop();
        $docNop->setDocComment(new Doc($prependVarTypesDocBlocks));

        // must be AFTER extract(), otherwise the variable does not exists
        $firstNode = array_shift($node->stmts);
        $classMethodStmts = array_merge([$firstNode], [$docNop], $node->stmts);

        $node->stmts = $classMethodStmts;
        return $node;
    }
}

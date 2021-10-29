<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\PhpParser;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Symplify\Astral\PhpParser\SmartPhpParser;

/**
 * @api
 */
final class ParentNodeAwarePhpParser
{
    public function __construct(
        private SmartPhpParser $smartPhpParser
    ) {
    }

    /**
     * @return Stmt[]
     */
    public function parsePhpContent(string $phpContent): array
    {
        $phpStmts = $this->smartPhpParser->parseString($phpContent);
        if ($phpStmts === []) {
            return [];
        }

        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor(new ParentConnectingVisitor());
        $phpNodeTraverser->traverse($phpStmts);

        return $phpStmts;
    }
}

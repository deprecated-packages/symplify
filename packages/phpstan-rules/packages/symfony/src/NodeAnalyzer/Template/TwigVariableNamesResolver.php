<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer\Template;

use PhpParser\Node\Stmt;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\Parser;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Symfony\PhpParser\NodeVisitor\CollectUsedVariablesNodeVisitor;
use Symplify\PHPStanRules\TwigPHPStanPrinter\TwigToPhpCompiler;

final class TwigVariableNamesResolver
{
    public function __construct(
        private TwigToPhpCompiler $twigToPhpCompiler,
        private Parser $parser,
        private SimpleNameResolver $simpleNameResolver,
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $phpFileContent = $this->twigToPhpCompiler->compileContent($filePath, []);

        $stmts = $this->parser->parse($phpFileContent);
        if ($stmts === null) {
            return [];
        }

        $this->decorateParentAttribute($stmts);

        $nodeTraverser = new NodeTraverser();
        $collectUsedVariablesNodeVisitor = new CollectUsedVariablesNodeVisitor(
            $this->simpleNameResolver,
            $this->nodeFinder
        );
        $nodeTraverser->addVisitor($collectUsedVariablesNodeVisitor);

        $nodeTraverser->traverse($stmts);

        return $collectUsedVariablesNodeVisitor->getUsedVariableNames();
    }

    /**
     * @param Stmt[] $stmts
     */
    private function decorateParentAttribute(array $stmts): void
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NodeConnectingVisitor());
        $nodeTraverser->traverse($stmts);
    }
}

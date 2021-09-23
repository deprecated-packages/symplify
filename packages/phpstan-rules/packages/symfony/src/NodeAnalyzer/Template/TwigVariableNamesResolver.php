<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer\Template;

use PhpParser\Node\Stmt;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\Parser;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\TemplateVariableCollectingNodeVisitor;
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
        $templateVariableCollectingNodeVisitor = new TemplateVariableCollectingNodeVisitor(
            ['context', 'macros', 'this', '_parent', 'loop', 'tmp'],
            ['doDisplay'],
            $this->simpleNameResolver,
            $this->nodeFinder
        );
        $nodeTraverser->addVisitor($templateVariableCollectingNodeVisitor);

        $nodeTraverser->traverse($stmts);

        return $templateVariableCollectingNodeVisitor->getUsedVariableNames();
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

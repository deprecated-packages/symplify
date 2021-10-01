<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\ErrorReporting;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\PhpToTemplateLinesNodeVisitor;

final class TemplateLinesMapResolver
{
    public function __construct(
        private Parser $parser,
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function resolve(string $phpContent): array
    {
        $stmts = $this->parser->parse($phpContent);
        if ($stmts === null) {
            return [];
        }

        $phpToTemplateLinesNodeVisitor = new PhpToTemplateLinesNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($phpToTemplateLinesNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $phpToTemplateLinesNodeVisitor->getPhpLinesToTemplateLines();
    }
}

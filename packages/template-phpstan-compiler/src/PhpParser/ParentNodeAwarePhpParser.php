<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\PhpParser;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\ParserFactory;

/**
 * @api
 */
final class ParentNodeAwarePhpParser
{
    /**
     * @return Stmt[]
     */
    public function parsePhpContent(string $phpContent): array
    {
        $phpNodes = $this->parsePhpContentToPhpNodes($phpContent);
        if ($phpNodes === null) {
            return [];
        }

        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor(new ParentConnectingVisitor());
        $phpNodeTraverser->traverse($phpNodes);

        return $phpNodes;
    }

    /**
     * @return Stmt[]|null
     */
    private function parsePhpContentToPhpNodes(string $compiledPhp): ?array
    {
        $parserFactory = new ParserFactory();
        $phpParser = $parserFactory->create(ParserFactory::PREFER_PHP7);

        return $phpParser->parse($compiledPhp);
    }
}

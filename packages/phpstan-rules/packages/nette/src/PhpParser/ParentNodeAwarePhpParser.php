<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\PhpParser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\ParserFactory;

final class ParentNodeAwarePhpParser
{
    /**
     * @return Node[]|null
     */
    public function parsePhpContent(string $phpContent): array|null
    {
        $phpNodes = $this->parsePhpContentToPhpNodes($phpContent);
        if ($phpNodes === null) {
            return null;
        }

        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor(new ParentConnectingVisitor());
        $phpNodeTraverser->traverse($phpNodes);

        return $phpNodes;
    }

    /**
     * @return Node[]|null
     */
    private function parsePhpContentToPhpNodes(string $compiledPhp): ?array
    {
        $parserFactory = new ParserFactory();
        $phpParser = $parserFactory->create(ParserFactory::PREFER_PHP7);

        return $phpParser->parse($compiledPhp);
    }
}

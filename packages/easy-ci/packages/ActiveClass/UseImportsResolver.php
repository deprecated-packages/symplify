<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symplify\EasyCI\NodeVisitor\UsedClassNodeVisitor;
use Symplify\SmartFileSystem\SmartFileInfo;

final class UseImportsResolver
{
    private Parser $parser;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolveFromFileInfos(array $phpFileInfos): array
    {
        $usedNames = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            // @todo maybe parse and traverse?
            $stmts = $this->parser->parse($phpFileInfo->getContents());
            if ($stmts === null) {
                continue;
            }

            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor(new NameResolver());
            $nodeTraverser->addVisitor(new NodeConnectingVisitor());
            $nodeTraverser->traverse($stmts);

            $nodeTraverser = new NodeTraverser();
            $usedClassNodeVisitor = new UsedClassNodeVisitor();
            $nodeTraverser->addVisitor($usedClassNodeVisitor);
            $nodeTraverser->traverse($stmts);

            $usedNames = array_merge($usedNames, $usedClassNodeVisitor->getUsedNames());
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }
}

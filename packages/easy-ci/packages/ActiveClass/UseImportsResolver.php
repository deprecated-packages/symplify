<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\UsedClassNodeVisitor;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\UseImportsResolverTest
 */
final class UseImportsResolver
{
    public function __construct(
        private Parser $parser,
        private FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator,
    ) {
    }

    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolveFromFileInfos(array $phpFileInfos): array
    {
        $usedNames = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            $usedNames = array_merge($usedNames, $this->resolve($phpFileInfo));
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }

    /**
     * @return string[]
     */
    public function resolve(SmartFileInfo $phpFileInfo): array
    {
        $stmts = $this->parser->parse($phpFileInfo->getContents());
        if ($stmts === null) {
            return [];
        }

        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

        $nodeTraverser = new NodeTraverser();
        $usedClassNodeVisitor = new UsedClassNodeVisitor();
        $nodeTraverser->addVisitor($usedClassNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $usedClassNodeVisitor->getUsedNames();
    }
}

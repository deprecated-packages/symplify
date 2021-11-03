<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Console\Style\SymfonyStyle;
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
        private SymfonyStyle $symfonyStyle,
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
            $this->symfonyStyle->progressAdvance();

            $stmts = $this->parser->parse($phpFileInfo->getContents());
            if ($stmts === null) {
                continue;
            }

            $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

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

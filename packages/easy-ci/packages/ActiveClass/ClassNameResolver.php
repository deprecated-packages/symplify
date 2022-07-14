<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\ClassNameResolver\ClassNameResolverTest
 */
final class ClassNameResolver
{
    public function __construct(
        private Parser $parser,
        private FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator
    ) {
    }

    /**
     * @param SmartFileInfo[]|SplFileInfo[] $fileInfos
     * @return string[]
     */
    public function resolveFromFromFileInfos(array $fileInfos): array
    {
        $classNames = [];
        foreach ($fileInfos as $fileInfo) {
            $className = $this->resolveFromFromFileInfo($fileInfo);
            if ($className === null) {
                continue;
            }

            $classNames[] = $className;
        }

        return $classNames;
    }

    /**
     * @api
     */
    public function resolveFromFromFileInfo(SmartFileInfo|SplFileInfo $fileInfo): ?string
    {
        $stmts = $this->parser->parse($fileInfo->getContents());
        if ($stmts === null) {
            return null;
        }

        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

        $classNameNodeVisitor = new ClassNameNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($classNameNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $classNameNodeVisitor->getClassName();
    }
}

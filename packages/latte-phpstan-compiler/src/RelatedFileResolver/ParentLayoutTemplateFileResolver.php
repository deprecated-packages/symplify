<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\RelatedFileResolver;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\ParentLayoutNameNodeVisitor;

final class ParentLayoutTemplateFileResolver
{
    public function __construct(
        private ParentLayoutNameNodeVisitor $parentLayoutNameNodeVisitor,
    ) {
    }

    /**
     * @param Stmt[] $phpNodes
     */
    public function resolve(string $templateFilePath, array $phpNodes): ?string
    {
        $phpNodeTraverser = new NodeTraverser();
        $this->parentLayoutNameNodeVisitor->setTemplateFilePath($templateFilePath);

        $phpNodeTraverser->addVisitor($this->parentLayoutNameNodeVisitor);
        $phpNodeTraverser->traverse($phpNodes);

        return $this->parentLayoutNameNodeVisitor->getParentLayoutFileName();
    }
}

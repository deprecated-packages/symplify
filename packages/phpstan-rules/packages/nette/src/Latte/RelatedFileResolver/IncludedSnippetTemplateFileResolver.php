<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Latte\RelatedFileResolver;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\TemplateIncludesNameNodeVisitor;

final class IncludedSnippetTemplateFileResolver
{
    public function __construct(
        private TemplateIncludesNameNodeVisitor $templateIncludesNameNodeVisitor
    ) {
    }

    /**
     * @param Stmt[] $phpNodes
     * @return string[]
     */
    public function resolve(string $templateFilePath, array $phpNodes): array
    {
        // resolve included templates
        $this->templateIncludesNameNodeVisitor->setTemplateFilePath($templateFilePath);

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->templateIncludesNameNodeVisitor);
        $nodeTraverser->traverse($phpNodes);

        return $this->templateIncludesNameNodeVisitor->getIncludedTemplateFilePaths();
    }
}

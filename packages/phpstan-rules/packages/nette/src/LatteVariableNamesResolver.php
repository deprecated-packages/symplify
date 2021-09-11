<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\Nette\Latte\LatteToPhpCompiler;
use Symplify\PHPStanRules\Nette\PhpNodeVisitor\LatteVariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\ParentNodeAwarePhpParser;

final class LatteVariableNamesResolver
{
    public function __construct(
        private ParentNodeAwarePhpParser $parentNodeAwarePhpParser,
        private LatteToPhpCompiler $latteToPhpCompiler
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $compiledPhp = $this->latteToPhpCompiler->compileFilePath($filePath);

        $phpNodes = $this->parentNodeAwarePhpParser->parsePhpContent($compiledPhp);
        if ($phpNodes === null) {
            return [];
        }

        $latteVariableCollectingNodeVisitor = new LatteVariableCollectingNodeVisitor();
        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor($latteVariableCollectingNodeVisitor);
        $phpNodeTraverser->traverse($phpNodes);

        return $latteVariableCollectingNodeVisitor->getUsedVariableNames();
    }
}

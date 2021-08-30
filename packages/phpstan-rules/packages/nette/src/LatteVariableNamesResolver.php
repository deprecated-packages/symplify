<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Latte\Parser;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\Nette\Latte\UnknownMacroAwareLatteCompiler;
use Symplify\PHPStanRules\Nette\PhpNodeVisitor\LatteVariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\ParentNodeAwarePhpParser;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteVariableNamesResolver
{
    public function __construct(
        private Parser $latteParser,
        private SmartFileSystem $smartFileSystem,
        private ParentNodeAwarePhpParser $parentNodeAwarePhpParser,
        private UnknownMacroAwareLatteCompiler $unknownMacroAwareLatteCompiler,
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $latteTokens = $this->latteParser->parse($fileContent);

        // collect used variable from PHP
        $compiledPhp = $this->unknownMacroAwareLatteCompiler->compile($latteTokens, 'DummyTemplateClass');

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

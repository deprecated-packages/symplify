<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Latte\Compiler;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Latte\Parser;
use Latte\Runtime\Defaults;
use Nette\Bridges\ApplicationLatte\UIMacros;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\Nette\PhpNodeVisitor\LatteVariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\ParentNodeAwarePhpParser;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteVariableNamesResolver
{
    private Compiler $latteCompiler;

    public function __construct(
        private Parser $latteParser,
        private SmartFileSystem $smartFileSystem,
        private ParentNodeAwarePhpParser $parentNodeAwarePhpParser
    ) {
        $this->latteCompiler = new Compiler();
        CoreMacros::install($this->latteCompiler);
        BlockMacros::install($this->latteCompiler);
        UIMacros::install($this->latteCompiler);

        $runtimeDefaults = new Defaults();

        $functionNames = array_keys($runtimeDefaults->getFunctions());
        $this->latteCompiler->setFunctions($functionNames);
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $latteTokens = $this->latteParser->parse($fileContent);

        // collect used variable from PHP
        $compiledPhp = $this->latteCompiler->compile($latteTokens, 'DummyTemplateClass');

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

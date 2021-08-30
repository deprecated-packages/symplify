<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Latte\Compiler;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Latte\Parser;
use Latte\Runtime\Defaults;
use Nette\Bridges\ApplicationLatte\UIMacros;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\ParserFactory;
use Symplify\PHPStanRules\Nette\PhpNodeVisitor\LatteVariableCollectingNodeVisitor;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteVariableNamesResolver
{
    private Compiler $latteCompiler;

    public function __construct(
        private Parser $latteParser,
        private SmartFileSystem $smartFileSystem
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

        $phpNodes = $this->parsePhpContentToPhpNodes($compiledPhp);
        if ($phpNodes === null) {
            return [];
        }

        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor(new ParentConnectingVisitor());
        $phpNodeTraverser->traverse($phpNodes);

        $latteVariableCollectingNodeVisitor = new LatteVariableCollectingNodeVisitor();
        $phpNodeTraverser->addVisitor($latteVariableCollectingNodeVisitor);
        $phpNodeTraverser->traverse($phpNodes);

        return $latteVariableCollectingNodeVisitor->getUsedVariableNames();
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

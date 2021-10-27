<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler;

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\PhpParser\SmartPhpParser;
use Symplify\LattePHPStanCompiler\Exception\LattePHPStanCompilerException;
use Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor\AppendExtractedVarTypesNodeVisitor;
use Symplify\TemplatePHPStanCompiler\NodeFactory\VarDocNodeFactory;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

final class LatteVarTypeDocBlockDecorator
{
    public function __construct(
        private SmartPhpParser $smartPhpParser,
        private Standard $printerStandard,
        private SimpleNameResolver $simpleNameResolver,
        private VarDocNodeFactory $varDocNodeFactory,
    ) {
    }

    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    public function decorateLatteContentWithTypes(string $phpContent, array $variablesAndTypes): string
    {
        // convert to "@var types $variable"
        $phpStmts = $this->smartPhpParser->parseString($phpContent);
        if ($phpStmts === []) {
            throw new LattePHPStanCompilerException();
        }

        $nodeTraverser = new NodeTraverser();
        $appendExtractedVarTypesNodeVisitor = new AppendExtractedVarTypesNodeVisitor(
            $this->simpleNameResolver,
            $this->varDocNodeFactory,
            $variablesAndTypes
        );

        $nodeTraverser->addVisitor($appendExtractedVarTypesNodeVisitor);
        $nodeTraverser->traverse($phpStmts);

        $printedPhpContent = $this->printerStandard->prettyPrintFile($phpStmts);
        return rtrim($printedPhpContent) . PHP_EOL;
    }
}

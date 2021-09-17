<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor\AppendExtractedVarTypesNodeVisitor;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\VariableAndType;

final class VarTypeDocBlockDecorator
{
    public function __construct(
        private Parser $phpParser,
        private Standard $printerStandard,
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    public function decorateLatteContentWithTypes(string $phpContent, array $variablesAndTypes): string
    {
        // convert to "@var types $variable"
        $phpNodes = $this->phpParser->parse($phpContent);
        if ($phpNodes === null) {
            throw new ShouldNotHappenException();
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(
            new AppendExtractedVarTypesNodeVisitor($this->simpleNameResolver, $variablesAndTypes)
        );
        $nodeTraverser->traverse($phpNodes);

        $printedPhpContent = $this->printerStandard->prettyPrintFile($phpNodes);
        return rtrim($printedPhpContent) . PHP_EOL;
    }
}

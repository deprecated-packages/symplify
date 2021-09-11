<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Lexer;
use PhpParser\Node\Expr\Array_;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\Nette\Latte\LatteToPhpCompiler;
use Symplify\PHPStanRules\Nette\Latte\Tokens\PhpToLatteLineNumbersResolver;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\AppendExtractedVarTypesNodeVisitor;
use Symplify\PHPStanRules\Nette\ValueObject\PhpFileContentsWithLineMap;
use Symplify\PHPStanRules\Symfony\TypeAnalyzer\TemplateVariableTypesResolver;

final class TemplateFileVarTypeDocBlocksDecorator
{
    public function __construct(
        private LatteToPhpCompiler $latteToPhpCompiler,
        private TemplateVariableTypesResolver $templateVariableTypesResolver,
        private PhpToLatteLineNumbersResolver $phpToLatteLineNumbersResolver,
        private Standard $printerStandard
    ) {
    }

    public function decorate(string $filePath, Array_ $array, Scope $scope): PhpFileContentsWithLineMap
    {
        $phpContent = $this->latteToPhpCompiler->compileFilePath($filePath);

        // traverse nodes to add types after \DummyTemplateClass::main()
        $variablesAndTypes = $this->templateVariableTypesResolver->resolveArray($array, $scope);
        $variablesAndTypesCount = count($variablesAndTypes);

        // convert to "@var types $variable"
        $nodeTraverser = new NodeTraverser();

        $phpLexer = new Lexer();
        $parser = $this->createParserFromLexer($phpLexer);
        $phpNodes = $parser->parse($phpContent);
        if ($phpNodes === null) {
            throw new ShouldNotHappenException();
        }

        $nodeTraverser->addVisitor(new AppendExtractedVarTypesNodeVisitor($variablesAndTypes));
        $nodeTraverser->traverse($phpNodes);

        $decoratedPhpContent = $this->printerStandard->prettyPrintFile($phpNodes);

        $phpTokens = $phpLexer->getTokens();
        $phpLinesToLatteLines = $this->phpToLatteLineNumbersResolver->resolve($phpTokens, $variablesAndTypesCount);

        return new PhpFileContentsWithLineMap($decoratedPhpContent, $phpLinesToLatteLines);
    }

    private function createParserFromLexer(Lexer $phpLexer): Parser
    {
        $parserFactory = new ParserFactory();
        return $parserFactory->create(ParserFactory::PREFER_PHP7, $phpLexer);
    }
}

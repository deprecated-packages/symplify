<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Expr\Array_;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use stdClass;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Tokens\PhpToLatteLineNumbersResolver;
use Symplify\PHPStanRules\LattePHPStanPrinter\LatteToPhpCompiler;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\PhpFileContentsWithLineMap;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\AppendExtractedVarTypesNodeVisitor;
use Symplify\PHPStanRules\Symfony\TypeAnalyzer\TemplateVariableTypesResolver;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndType;

final class TemplateFileVarTypeDocBlocksDecorator
{
    public function __construct(
        private LatteToPhpCompiler $latteToPhpCompiler,
        private TemplateVariableTypesResolver $templateVariableTypesResolver,
        private PhpToLatteLineNumbersResolver $phpToLatteLineNumbersResolver,
        private Standard $printerStandard,
        private Parser $phpParser
    ) {
    }

    public function decorate(string $latteFilePath, Array_ $array, Scope $scope): PhpFileContentsWithLineMap
    {
        $phpContent = $this->latteToPhpCompiler->compileFilePath($latteFilePath);

        $variablesAndTypes = $this->resolveLatteVariablesAndTypes($array, $scope);

        // convert to "@var types $variable"
        $phpNodes = $this->phpParser->parse($phpContent);
        if ($phpNodes === null) {
            throw new ShouldNotHappenException();
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new AppendExtractedVarTypesNodeVisitor($variablesAndTypes));
        $nodeTraverser->traverse($phpNodes);

        $decoratedPhpContent = $this->printerStandard->prettyPrintFile($phpNodes);

        $phpLinesToLatteLines = $this->phpToLatteLineNumbersResolver->resolve($decoratedPhpContent);
        return new PhpFileContentsWithLineMap($decoratedPhpContent, $phpLinesToLatteLines);
    }

    /**
     * @return VariableAndType[]
     */
    private function resolveLatteVariablesAndTypes(Array_ $array, Scope $scope): array
    {
        // traverse nodes to add types after \DummyTemplateClass::main()
        $variablesAndTypes = $this->templateVariableTypesResolver->resolveArray($array, $scope);
        $defaultNetteVariablesAndTypes = $this->createDefaultNetteVariablesAndTypes();

        return array_merge($variablesAndTypes, $defaultNetteVariablesAndTypes);
    }

    /**
     * @return VariableAndType[]
     */
    private function createDefaultNetteVariablesAndTypes(): array
    {
        $variablesAndTypes = [];
        $variablesAndTypes[] = new VariableAndType('baseUrl', new StringType());
        $variablesAndTypes[] = new VariableAndType('basePath', new StringType());

        // nette\security bridge
        $variablesAndTypes[] = new VariableAndType('user', new ObjectType('Nette\Security\User'));

        // nette\application bridge
        $variablesAndTypes[] = new VariableAndType('presenter', new ObjectType('Nette\Application\UI\Presenter'));
        $variablesAndTypes[] = new VariableAndType('control', new ObjectType('Nette\Application\UI\Control'));
        $variablesAndTypes[] = new VariableAndType('flashes', new ObjectType(stdClass::class));

        return $variablesAndTypes;
    }
}

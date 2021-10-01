<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler;

use PhpParser\Node\Expr\Array_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use stdClass;
use Symplify\LattePHPStanCompiler\Latte\Tokens\PhpToLatteLineNumbersResolver;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\TemplatePHPStanCompiler\ValueObject\PhpFileContentsWithLineMap;
use Symplify\TemplatePHPStanCompiler\TypeAnalyzer\TemplateVariableTypesResolver;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

final class TemplateFileVarTypeDocBlocksDecorator
{
    public function __construct(
        private LatteToPhpCompiler $latteToPhpCompiler,
        private PhpToLatteLineNumbersResolver $phpToLatteLineNumbersResolver,
        private TemplateVariableTypesResolver $templateVariableTypesResolver
    ) {
    }

    /**
     * @param ComponentNameAndType[] $componentNamesAndTypes
     */
    public function decorate(
        string $latteFilePath,
        Array_ $array,
        Scope $scope,
        array $componentNamesAndTypes
    ): PhpFileContentsWithLineMap {
        $variablesAndTypes = $this->resolveLatteVariablesAndTypes($array, $scope);

        $phpContent = $this->latteToPhpCompiler->compileFilePath(
            $latteFilePath,
            $variablesAndTypes,
            $componentNamesAndTypes
        );

        $phpLinesToLatteLines = $this->phpToLatteLineNumbersResolver->resolve($phpContent);
        return new PhpFileContentsWithLineMap($phpContent, $phpLinesToLatteLines);
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

        $flashesArrayType = new ArrayType(new MixedType(), new ObjectType(stdClass::class));
        $variablesAndTypes[] = new VariableAndType('flashes', $flashesArrayType);

        return $variablesAndTypes;
    }
}

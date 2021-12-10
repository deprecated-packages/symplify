<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler;

use PhpParser\Node\Expr\Array_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Symplify\LattePHPStanCompiler\Contract\LatteVariableCollectorInterface;
use Symplify\LattePHPStanCompiler\Latte\Tokens\PhpToLatteLineNumbersResolver;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\TemplatePHPStanCompiler\TypeAnalyzer\TemplateVariableTypesResolver;
use Symplify\TemplatePHPStanCompiler\ValueObject\PhpFileContentsWithLineMap;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

/**
 * @api
 */
final class TemplateFileVarTypeDocBlocksDecorator
{
    /**
     * @param LatteVariableCollectorInterface[] $latteVariableCollectors
     */
    public function __construct(
        private LatteToPhpCompiler $latteToPhpCompiler,
        private PhpToLatteLineNumbersResolver $phpToLatteLineNumbersResolver,
        private TemplateVariableTypesResolver $templateVariableTypesResolver,
        private array $latteVariableCollectors
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
        foreach ($this->latteVariableCollectors as $latteVariableCollector) {
            $collectedVariablesAndTypes = $latteVariableCollector->getVariablesAndTypes();
            $variablesAndTypes = array_merge($variablesAndTypes, $collectedVariablesAndTypes);
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection) {
            $variablesAndTypes[] = new VariableAndType('actualClass', new ObjectType($classReflection->getName()));

            // scope: presenter -> control and presenter is the presenter = actualclass
            // scope: control -> control is actualclass, presenter - we dont know from this context

        }

        return $variablesAndTypes;
    }
}

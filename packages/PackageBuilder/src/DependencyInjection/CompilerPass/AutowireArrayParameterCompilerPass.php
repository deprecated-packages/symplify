<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Nette\Application\UI\MethodReflection;
use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

/**
 * @inspiration https://github.com/nette/di/pull/178
 */
final class AutowireArrayParameterCompilerPass implements CompilerPassInterface
{
    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    public function __construct()
    {
        $this->definitionFinder = new DefinitionFinder();
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if ($this->shouldSkipDefinition($containerBuilder, $definition)) {
                continue;
            }

            /** @var ReflectionClass $reflectionClass */
            $reflectionClass = $containerBuilder->getReflectionClass($definition->getClass());
            /** @var MethodReflection $constructorMethodReflection */
            $constructorMethodReflection = $reflectionClass->getConstructor();

            $this->processParameters($containerBuilder, $constructorMethodReflection, $definition);
        }
    }

    private function processParameters(
        ContainerBuilder $containerBuilder,
        ReflectionMethod $reflectionMethod,
        Definition $definition
    ): void {
        foreach ($reflectionMethod->getParameters() as $parameterReflection) {
            if (! $parameterReflection->isArray()) {
                continue;
            }

            // already set
            if (isset($definition->getArguments()['$' . $parameterReflection->getName()])) {
                continue;
            }

            $parameterType = $this->resolveParameterType($parameterReflection->getName(), $reflectionMethod);
            if ($parameterType === null) {
                continue;
            }

            if (! class_exists($parameterType) && ! interface_exists($parameterType)) {
                continue;
            }

            $definitionsOfType = $this->definitionFinder->findAllByType($containerBuilder, $parameterType);

            $argumentName = '$' . $parameterReflection->getName();
            $definition->setArgument($argumentName, $this->createReferencesFromDefinitions($definitionsOfType));
        }
    }

    private function resolveParameterType(string $parameterName, ReflectionMethod $reflectionMethod): ?string
    {
        $parameterDocTypeRegex = '#@param[ \t]+(?<type>[\w\\\\]+)\[\][ \t]+\$' . $parameterName . '#';

        // copied from https://github.com/nette/di/blob/d1c0598fdecef6d3b01e2ace5f2c30214b3108e6/src/DI/Autowiring.php#L215
        $result = Strings::match((string) $reflectionMethod->getDocComment(), $parameterDocTypeRegex);
        if ($result === null) {
            return null;
        }

        // not a class|interface type
        if (ctype_lower($result['type'][0])) {
            return null;
        }

        return Reflection::expandClassName($result['type'], $reflectionMethod->getDeclaringClass());
    }

    private function shouldSkipDefinition(ContainerBuilder $containerBuilder, Definition $definition): bool
    {
        if ($definition->isAbstract()) {
            return true;
        }

        if ($definition->getClass() === null) {
            return true;
        }

        if (! class_exists($definition->getClass())) {
            return true;
        }

        $reflectionClass = $containerBuilder->getReflectionClass($definition->getClass());
        if ($reflectionClass === null) {
            return true;
        }

        if (! $reflectionClass->hasMethod('__construct')) {
            return true;
        }

        /** @var MethodReflection $constructorMethodReflection */
        $constructorMethodReflection = $reflectionClass->getConstructor();
        if (! $constructorMethodReflection->getParameters()) {
            return true;
        }

        return false;
    }

    /**
     * @param Definition[] $definitions
     * @return Reference[]
     */
    private function createReferencesFromDefinitions(array $definitions): array
    {
        $references = [];
        foreach (array_keys($definitions) as $definitionOfTypeName) {
            $references[] = new Reference($definitionOfTypeName);
        }

        return $references;
    }
}

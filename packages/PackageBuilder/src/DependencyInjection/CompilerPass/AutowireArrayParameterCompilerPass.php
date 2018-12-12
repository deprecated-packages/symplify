<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Nette\Application\UI\MethodReflection;
use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
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
     * Classes that create circular dependencies
     * @var string[]
     */
    private $excludedPossibleFatalClasses = [];

    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    /**
     * @param string[] $excludedPossibleFatalClasses
     */
    public function __construct(array $excludedPossibleFatalClasses = [
        'Symfony\Component\Form\FormExtensionInterface',
        'Symfony\Component\Asset\PackageInterface',
        'Symfony\Component\Config\Loader\LoaderInterface',
        'Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface',
        'EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\TypeConfiguratorInterface',
    ])
    {
        $this->definitionFinder = new DefinitionFinder();
        $this->excludedPossibleFatalClasses = $excludedPossibleFatalClasses;
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

    private function shouldSkipDefinition(ContainerBuilder $containerBuilder, Definition $definition): bool
    {
        if ($definition->isAbstract()) {
            return true;
        }

        if ($definition->getClass() === null) {
            return true;
        }

        if (in_array($definition->getClass(), $this->excludedPossibleFatalClasses, true)) {
            return true;
        }

        if ($definition->getFactory()) {
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

    private function processParameters(
        ContainerBuilder $containerBuilder,
        ReflectionMethod $reflectionMethod,
        Definition $definition
    ): void {
        foreach ($reflectionMethod->getParameters() as $parameterReflection) {
            if ($this->shouldSkipParameter($reflectionMethod, $definition, $parameterReflection)) {
                continue;
            }

            $parameterType = $this->resolveParameterType($parameterReflection->getName(), $reflectionMethod);
            $definitionsOfType = $this->definitionFinder->findAllByType($containerBuilder, $parameterType);
            $definitionsOfType = $this->filterOutAbstractDefinitions($definitionsOfType);

            $argumentName = '$' . $parameterReflection->getName();
            $definition->setArgument($argumentName, $this->createReferencesFromDefinitions($definitionsOfType));
        }
    }

    private function shouldSkipParameter(
        ReflectionMethod $reflectionMethod,
        Definition $definition,
        ReflectionParameter $reflectionParameter
    ): bool {
        if (! $reflectionParameter->isArray()) {
            return true;
        }

        // already set
        $argumentName = '$' . $reflectionParameter->getName();
        if (isset($definition->getArguments()[$argumentName])) {
            return true;
        }

        $parameterType = $this->resolveParameterType($reflectionParameter->getName(), $reflectionMethod);
        if ($parameterType === null) {
            return true;
        }

        if (in_array($parameterType, $this->excludedPossibleFatalClasses, true)) {
            return true;
        }

        if (! class_exists($parameterType) && ! interface_exists($parameterType)) {
            return true;
        }

        // prevent circular dependency
        if (is_a($definition->getClass(), $parameterType, true)) {
            return true;
        }

        return false;
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

    /**
     * Abstract definitions cannot be the target of references
     *
     * @param Definition[] $definitions
     * @return Definition[]
     */
    private function filterOutAbstractDefinitions(array $definitions): array
    {
        foreach ($definitions as $key => $definition) {
            if ($definition->isAbstract()) {
                unset($definitions[$key]);
            }
        }

        return $definitions;
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

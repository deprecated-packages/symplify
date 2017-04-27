<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\DependencyInjection;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

final class DefinitionAnalyzer
{
    /**
     * @var DefinitionValidator
     */
    private $definitionValidator;

    /**
     * @var MethodAnalyzer
     */
    private $methodAnalyzer;

    public function __construct(DefinitionValidator $definitionValidator, MethodAnalyzer $methodAnalyzer)
    {
        $this->definitionValidator = $definitionValidator;
        $this->methodAnalyzer = $methodAnalyzer;
    }

    public function shouldDefinitionBeAutowired(ContainerBuilder $containerBuilder, Definition $definition): bool
    {
        if (! $this->definitionValidator->validate($definition)) {
            return false;
        }

        $isFactory = $definition->getFactory() !== null;
        if ($isFactory) {
            return $this->shouldFactoryBuiltDefinitionBeAutowired($containerBuilder, $definition);
        }

        return $this->shouldClassDefinitionBeAutowired($definition);
    }

    private function shouldFactoryBuiltDefinitionBeAutowired(
        ContainerBuilder $containerBuilder,
        Definition $definition
    ): bool {
        $factory = $definition->getFactory();

        // functions specified as string are not supported
        if (is_string($factory)) {
            return false;
        }

        $factoryMethodReflection = $this->createFactoryMethodReflection($containerBuilder, $factory);

        if (! $this->methodAnalyzer->hasMethodWithMissingArgumentTypehints($factoryMethodReflection, $definition)) {
            return false;
        }

        return true;
    }

    private function shouldClassDefinitionBeAutowired(Definition $definition): bool
    {
        $classReflection = new ReflectionClass($definition->getClass());

        if (! $classReflection->hasMethod('__construct')) {
            return false;
        }

        $constructorReflection = $classReflection->getConstructor();
        if (! $this->methodAnalyzer->hasMethodWithMissingArgumentTypehints($constructorReflection, $definition)) {
            return false;
        }

        return true;
    }

    /**
     * @param string[]|Reference[] $factory
     */
    private function createFactoryMethodReflection(ContainerBuilder $containerBuilder, array $factory): ReflectionMethod
    {
        [$class, $method] = $factory;
        if ($class instanceof Reference) {
            $factoryClassDefinition = $containerBuilder->findDefinition($class);
            if ($factoryClassDefinition instanceof DefinitionDecorator) {
                $factoryClassDefinition = $containerBuilder->findDefinition($factoryClassDefinition->getParent());
            }

            $class = $factoryClassDefinition->getClass();
            if (strpos($class, '%') !== false) {
                $class = $containerBuilder->getParameter(str_replace('%', '', $class));
            }
        }

        return new ReflectionMethod($class, $method);
    }
}

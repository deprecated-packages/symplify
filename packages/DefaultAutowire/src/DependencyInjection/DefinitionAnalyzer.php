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

    public function __construct(DefinitionValidator $definitionValidator)
    {
        $this->definitionValidator = $definitionValidator;
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

        $factoryMethodReflection = new ReflectionMethod($class, $method);

        if (! $this->hasMethodArguments($factoryMethodReflection)) {
            return false;
        }

        if ($this->areAllMethodArgumentsRequired($definition, $factoryMethodReflection)) {
            return false;
        }

        if (! $this->haveMissingArgumentsTypehints($definition, $factoryMethodReflection)) {
            return false;
        }

        return true;
    }

    private function shouldClassDefinitionBeAutowired(Definition $definition): bool
    {
        $classReflection = new ReflectionClass($definition->getClass());
        if (! $classReflection->hasMethod('__construct')
            || ! $this->hasMethodArguments($classReflection->getConstructor())
        ) {
            return false;
        }

        $constructorReflection = $classReflection->getConstructor();
        if ($this->areAllMethodArgumentsRequired($definition, $constructorReflection)) {
            return false;
        }

        if (! $this->haveMissingArgumentsTypehints($definition, $constructorReflection)) {
            return false;
        }

        return true;
    }

    private function hasMethodArguments(ReflectionMethod $methodReflection): bool
    {
        return $methodReflection->getNumberOfParameters() !== 0;
    }

    private function areAllMethodArgumentsRequired(
        Definition $definition,
        ReflectionMethod $constructorReflection
    ): bool {
        $constructorArgumentsCount = count($definition->getArguments());
        $constructorRequiredArgumentsCount = $constructorReflection->getNumberOfRequiredParameters();

        if ($constructorArgumentsCount === $constructorRequiredArgumentsCount) {
            return true;
        }

        return false;
    }

    private function haveMissingArgumentsTypehints(
        Definition $definition,
        ReflectionMethod $constructorReflection
    ): bool {
        $arguments = $definition->getArguments();
        if (! count($arguments)) {
            return true;
        }

        $i = 0;
        foreach ($constructorReflection->getParameters() as $parameterReflection) {
            if (! isset($arguments[$i])) {
                if ($parameterReflection->isDefaultValueAvailable()) {
                    ++$i;
                    continue;
                }

                if (! $parameterReflection->getType()) {
                    ++$i;
                    continue;
                }

                if (! $parameterReflection->getType()->allowsNull()) {
                    return true;
                }
            }

            ++$i;
        }

        return false;
    }
}

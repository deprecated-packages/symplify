<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AutoReturnFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const RETURN_TYPE_PATTERN = '#\@return\s+(?<returnType>[\\\\\w]+)#';

    /**
     * @var string[]
     */
    private $excludedFactoryTypes = ['Symfony\Component\DependencyInjection\ContainerInterface'];

    public function process(ContainerBuilder $containerBuilder): void
    {
        $passiveFactoryClasses = $this->findFactoriesWithoutUsage($containerBuilder);
        foreach ($passiveFactoryClasses as $passiveFactoryClass) {
            if (! $this->isFactoryUseCandidate($passiveFactoryClass)) {
                continue;
            }

            $createReflectionMethod = new ReflectionMethod($passiveFactoryClass, 'create');
            $returnType = $this->resolveReturnType($createReflectionMethod);

            // register factory
            $containerBuilder->autowire($returnType)
                ->setPublic(true)
                ->setClass($returnType)
                ->setFactory([new Reference($passiveFactoryClass), 'create']);
        }
    }

    /**
     * @return string[]
     */
    private function findFactoriesWithoutUsage(ContainerBuilder $containerBuilder): array
    {
        $activeFactories = $this->findActiveFactories($containerBuilder);

        $passiveFactories = [];
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if ($definition->getClass() === null) {
                continue;
            }

            if (! Strings::endsWith($definition->getClass(), 'Factory')) {
                continue;
            }

            if (in_array($definition->getClass(), $activeFactories, true)) {
                continue;
            }

            $passiveFactories[] = $definition->getClass();
        }

        return $passiveFactories;
    }

    private function isFactoryUseCandidate(string $class): bool
    {
        $factoryReflectionClass = new ReflectionClass($class);
        if ($factoryReflectionClass->isAbstract()) {
            return false;
        }

        if (! $factoryReflectionClass->hasMethod('create')) {
            return false;
        }

        $createMethodReflection = $factoryReflectionClass->getMethod('create');
        $returnType = $this->resolveReturnType($createMethodReflection);
        if ($returnType === null) {
            return false;
        }

        // is must be existing class or an interface
        if (! class_exists($returnType) && ! interface_exists($returnType)) {
            return false;
        }

        if ($createMethodReflection->getNumberOfRequiredParameters() > 0) {
            return false;
        }

        if (in_array($returnType, $this->excludedFactoryTypes, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return string[]
     */
    private function findActiveFactories(ContainerBuilder $containerBuilder): array
    {
        $activeFactories = [];

        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (! $definition->getFactory()) {
                continue;
            }
            if (! isset($definition->getFactory()[0])) {
                continue;
            }

            $factoryClass = $definition->getFactory()[0];
            if ($factoryClass instanceof Reference) {
                $activeFactories[] = (string) $factoryClass;
            }
        }

        return $activeFactories;
    }

    private function resolveReturnType(ReflectionMethod $reflectionMethod)
    {
        if ($reflectionMethod->hasReturnType()) {
            return (string) $reflectionMethod->getReturnType();
        }

        $match = Strings::match((string) $reflectionMethod->getDocComment(), self::RETURN_TYPE_PATTERN);
        if (isset($match['returnType'])) {
            $classReflection = $reflectionMethod->getDeclaringClass();

            return Reflection::expandClassName($match['returnType'], $classReflection);
        }

        return null;
    }
}

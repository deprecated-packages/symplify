<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AutoReturnFactoryCompilerPass implements CompilerPassInterface
{
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
            $returnType = (string) $createReflectionMethod->getReturnType();

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

        // is must be existing class or an interface
        $returnType = (string) $createMethodReflection->getReturnType();
        if (! class_exists($returnType) && ! interface_exists($returnType)) {
            return false;
        }

        if ($createMethodReflection->getNumberOfParameters() > 0) {
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
}

<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\ControllerAutowire\Config\Definition\ConfigurationResolver;
use Symplify\ControllerAutowire\Contract\DependencyInjection\ControllerClassMapInterface;
use Symplify\ControllerAutowire\Contract\HttpKernel\ControllerFinderInterface;

final class RegisterControllersPass implements CompilerPassInterface
{
    /**
     * @var ControllerClassMapInterface
     */
    private $controllerClassMap;

    /**
     * @var ControllerFinderInterface
     */
    private $controllerFinder;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(
        ControllerClassMapInterface $controllerClassMap,
        ControllerFinderInterface $controllerFinder
    ) {
        $this->controllerClassMap = $controllerClassMap;
        $this->controllerFinder = $controllerFinder;
    }

    public function process(ContainerBuilder $containerBuilder) : void
    {
        $this->containerBuilder = $containerBuilder;

        $controllerDirs = $this->getControllerDirs();
        $controllers = $this->controllerFinder->findControllersInDirs($controllerDirs);
        $this->registerControllersToContainerBuilder($controllers);
    }

    /**
     * @return string[]
     */
    private function getControllerDirs() : array
    {
        $config = (new ConfigurationResolver())->resolveFromContainerBuilder($this->containerBuilder);

        return $config['controller_dirs'];
    }

    private function registerControllersToContainerBuilder(array $controllers) : void
    {
        foreach ($controllers as $id => $controller) {
            if (! $this->containerBuilder->hasDefinition($id)) {
                $definition = $this->buildControllerDefinitionFromClass($controller);
            } else {
                $definition = $this->containerBuilder->getDefinition($id);
                $definition->setAutowired(true);
            }

            $this->containerBuilder->setDefinition($id, $definition);
            $this->controllerClassMap->addController($id, $controller);
        }
    }

    private function buildControllerDefinitionFromClass(string $class) : Definition
    {
        $definition = new Definition($class);
        $definition->setAutowired(true);

        return $definition;
    }
}

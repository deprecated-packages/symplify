<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\DependencyInjection\Compiler;

use Nette\Utils\Strings;
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

    /**
     * @var string[]
     */
    private $alreadyRegisteredControllers = [];

    public function __construct(
        ControllerClassMapInterface $controllerClassMap,
        ControllerFinderInterface $controllerFinder
    ) {
        $this->controllerClassMap = $controllerClassMap;
        $this->controllerFinder = $controllerFinder;
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->containerBuilder = $containerBuilder;

        $controllerDirs = $this->getControllerDirs();
        $this->alreadyRegisteredControllers = $this->getAlreadyRegisteredControllers();
        $controllers = $this->controllerFinder->findControllersInDirs($controllerDirs);
        $this->registerControllersToContainerBuilder($controllers);
    }

    /**
     * @return string[]
     */
    private function getControllerDirs(): array
    {
        $config = (new ConfigurationResolver)->resolveFromContainerBuilder($this->containerBuilder);

        return $config['controller_dirs'];
    }

    /**
     * @param string[] $controllers
     */
    private function registerControllersToContainerBuilder(array $controllers): void
    {
        foreach ($controllers as $id => $controller) {
            if (array_key_exists($controller, $this->getAlreadyRegisteredControllers())) {
                $id = $this->alreadyRegisteredControllers[$controller];
                $definition = $this->containerBuilder->getDefinition($id);
                $definition->setAutowired(true);
            } else {
                $definition = $this->buildControllerDefinitionFromClass($controller);
            }

            $this->containerBuilder->setDefinition($id, $definition);
            $this->controllerClassMap->addController($id, $controller);
        }
    }

    private function buildControllerDefinitionFromClass(string $class): Definition
    {
        $definition = new Definition($class);
        $definition->setAutowired(true);

        return $definition;
    }

    /**
     * @return string[]
     */
    private function getAlreadyRegisteredControllers(): array
    {
        $controllers = [];
        $definitions = $this->containerBuilder->getDefinitions();

        foreach ($definitions as $serviceName => $definition) {
            if (! Strings::endsWith($definition->getClass(), 'Controller')) {
                continue;
            }

            $controllers[$definition->getClass()] = $serviceName;
        }

        return $controllers;
    }
}

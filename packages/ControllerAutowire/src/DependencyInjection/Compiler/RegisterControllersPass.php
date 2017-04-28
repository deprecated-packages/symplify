<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ControllerAutowire\Config\Definition\ConfigurationResolver;
use Symplify\ControllerAutowire\Contract\HttpKernel\ControllerFinderInterface;

final class RegisterControllersPass implements CompilerPassInterface
{
    /**
     * @var ControllerFinderInterface
     */
    private $controllerFinder;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(ControllerFinderInterface $controllerFinder)
    {
        $this->controllerFinder = $controllerFinder;
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->containerBuilder = $containerBuilder;

        $controllerDirs = $this->getControllerDirs();
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
        foreach ($controllers as $controller) {
            if ($this->containerBuilder->has($controller)) {
                $definition = $this->containerBuilder->getDefinition($controller);
                $definition->setAutowired(true);
            } else {
                $this->containerBuilder->autowire($controller, $controller);
            }
        }
    }
}

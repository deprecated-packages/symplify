<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\AutowireControllerDependenciesPass;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\DecorateControllerResolverPass;
use Symplify\ControllerAutowire\DependencyInjection\Compiler\RegisterControllersPass;
use Symplify\ControllerAutowire\DependencyInjection\ControllerClassMap;
use Symplify\ControllerAutowire\DependencyInjection\Extension\ContainerExtension;
use Symplify\ControllerAutowire\HttpKernel\Controller\ControllerFinder;

final class SymplifyControllerAutowireBundle extends Bundle
{
    /**
     * @var string
     */
    public const ALIAS = 'symplify_controller_autowire';

    public function build(ContainerBuilder $container)
    {
        $controllerClassMap = new ControllerClassMap();

        $container->addCompilerPass(new RegisterControllersPass($controllerClassMap, new ControllerFinder()));
        $container->addCompilerPass(new AutowireControllerDependenciesPass($controllerClassMap));
        $container->addCompilerPass(new DecorateControllerResolverPass($controllerClassMap));
    }

    public function createContainerExtension() : ContainerExtension
    {
        return new ContainerExtension();
    }
}

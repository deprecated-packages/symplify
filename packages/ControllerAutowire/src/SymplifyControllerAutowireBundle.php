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

    public function build(ContainerBuilder $containerBuilder) : void
    {
        $controllerClassMap = new ControllerClassMap();

        $containerBuilder->addCompilerPass(new RegisterControllersPass($controllerClassMap, new ControllerFinder()));
        $containerBuilder->addCompilerPass(new AutowireControllerDependenciesPass($controllerClassMap));
        $containerBuilder->addCompilerPass(new DecorateControllerResolverPass($controllerClassMap));
    }

    public function createContainerExtension() : ContainerExtension
    {
        return new ContainerExtension();
    }
}

<?php declare(strict_types=1);

namespace Symplify\ActionAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ActionAutowire\DependencyInjection\Container\ServicesByTypeMap;

final class AddServiceLocatorCompilerPass implements CompilerPassInterface
{
    /**
     * @var ServicesByTypeMap
     */
    private $servicesByTypeMap;

    public function __construct(ServicesByTypeMap $servicesByTypeMap)
    {
        $this->servicesByTypeMap = $servicesByTypeMap;
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->getDefinition('symplify.action_autowire.service_locator')
            ->addMethodCall('setServiceByTypeMap', [$this->servicesByTypeMap->getMap()]);
    }
}

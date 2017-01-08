<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Config\Definition;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ControllerAutowire\Contract\Config\Definition\ConfigurationResolverInterface;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;

final class ConfigurationResolver implements ConfigurationResolverInterface
{
    /**
     * @var string[]
     */
    private $resolvedConfiguration;

    public function resolveFromContainerBuilder(ContainerBuilder $containerBuilder) : array
    {
        if (! $this->resolvedConfiguration) {
            $processor = new Processor();
            $configs = $containerBuilder->getExtensionConfig(SymplifyControllerAutowireBundle::ALIAS);
            $configs = $processor->processConfiguration(new Configuration(), $configs);

            $this->resolvedConfiguration = $containerBuilder->getParameterBag()->resolveValue($configs);
        }

        return $this->resolvedConfiguration;
    }
}

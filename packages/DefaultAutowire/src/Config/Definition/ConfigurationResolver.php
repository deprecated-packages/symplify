<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Config\Definition;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle;

final class ConfigurationResolver
{
    /**
     * @var string[]
     */
    private $resolvedConfiguration;

    public function resolveFromContainerBuilder(ContainerBuilder $containerBuilder): array
    {
        if ($this->resolvedConfiguration) {
            return $this->resolvedConfiguration;
        }

        $processor = new Processor;
        $configs = $containerBuilder->getExtensionConfig(SymplifyDefaultAutowireBundle::ALIAS);
        $configs = $processor->processConfiguration(new Configuration, $configs);

        return $this->resolvedConfiguration = $containerBuilder->getParameterBag()
            ->resolveValue($configs);
    }
}

<?php declare(strict_types=1);

namespace Symplify\ServiceDefinitionDecorator\Adapter\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ServiceDefinitionDecorator\Adapter\Symfony\SymplifyServiceDefinitionDecoratorBundle;

/**
 * Inspired by https://github.com/nette/di/blob/master/src/DI/Extensions/DecoratorExtension.php.
 */
final class DecorateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $config = $containerBuilder->getExtensionConfig(SymplifyServiceDefinitionDecoratorBundle::ALIAS);
        if (! isset($config[0])) { // no configuration is set
            return;
        }

        $config = $config[0];

        foreach ($config as $type => $configuration) {
            foreach ($containerBuilder->getDefinitions() as $definition) {
                if (! is_a($definition->getClass(), $type, true)) {
                    continue;
                }

                $this->processCalls($configuration, $definition);
                $this->processTags($configuration, $definition);
                $this->processAutowire($configuration, $definition);
            }
        }
    }

    /**
     * @param mixed[] $configuration
     * @param Definition $definition
     */
    private function processCalls(array $configuration, Definition $definition): void
    {
        if (isset($configuration['calls'])) {
            $this->addCalls($definition, $configuration['calls']);
        }
    }

    /**
     * @param mixed[] $configuration
     * @param Definition $definition
     */
    private function processTags(array $configuration, Definition $definition): void
    {
        if (isset($configuration['tags'])) {
            $this->addTags($definition, $configuration['tags']);
        }
    }

    /**
     * @param mixed[] $configuration
     * @param Definition $definition
     */
    private function processAutowire($configuration, $definition): void
    {
        if (isset($configuration['autowire'])) {
            $this->addAutowired($definition, (bool)$configuration['autowire']);
        }
    }

    /**
     * @param Definition $definition
     * @param string[][] $setups
     */
    private function addCalls(Definition $definition, array $setups): void
    {
        foreach ($setups as $setterConfiguration) {
            [$methodName, $methodArguments] = $setterConfiguration;
            foreach ($methodArguments as $position => $methodArgument) {
                if (strpos($methodArgument, '@') === 0) {
                    $methodArguments[$position] = new Reference(substr($methodArgument, 1));
                }
            }

            $definition->addMethodCall($methodName, $methodArguments);
        }
    }

    /**
     * @param Definition $definition
     * @param string[][] $tags
     */
    private function addTags(Definition $definition, array $tags): void
    {
        foreach ($tags as $tag) {
            $name = key($tag);
            if ($name === 'name') {
                $value = reset($tag);
                $definition->addTag($value);
            }
        }
    }

    private function addAutowired(Definition $definition, bool $isAutowired): void
    {
        $definition->setAutowired($isAutowired);
    }
}

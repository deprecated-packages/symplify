<?php

declare(strict_types = 1);

namespace Symplify\ServiceDefinitionDecorator\Adapter\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\ServiceDefinitionDecorator\Adapter\Symfony\SymplifyServiceDefinitionDecoratorBundle;

/**
 * Inspired by https://github.com/nette/di/blob/master/src/DI/Extensions/DecoratorExtension.php
 */
final class DecorateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
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

                if (isset($configuration['calls'])) {
                    $this->addCalls($definition, $configuration['calls']);
                }

                if (isset($configuration['tags'])) {
                    $this->addTags($definition, $configuration['tags']);
                }

                if (isset($configuration['autowire'])) {
                    $this->addAutowired($definition, (bool) $configuration['autowire']);
                }
            }
        }
    }

    private function addCalls(Definition $definition, array $setups)
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

    private function addTags(Definition $definition, array $tags)
    {
        foreach ($tags as $tag) {
            $name = key($tag);
            if ($name === 'name') {
                $value = reset($tag);
                $definition->addTag($value);
            }
        }
    }

    private function addAutowired(Definition $definition, bool $isAutowired)
    {
        $definition->setAutowired($isAutowired);
    }
}

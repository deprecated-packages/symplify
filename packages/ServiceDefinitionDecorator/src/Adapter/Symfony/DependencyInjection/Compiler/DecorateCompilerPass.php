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
                $this->processDefinition($definition, $configuration, $type);
            }
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function processDefinition(Definition $definition, array $configuration, string $type): void
    {
        if (! is_a($definition->getClass(), $type, true)) {
            return;
        }

        $this->processCalls($configuration, $definition);
        $this->processTags($configuration, $definition);
        $this->processAutowire($configuration, $definition);
    }

    /**
     * @param mixed[] $configuration
     */
    private function processCalls(array $configuration, Definition $definition): void
    {
        if (isset($configuration['calls'])) {
            $this->addCalls($definition, $configuration['calls']);
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function processTags(array $configuration, Definition $definition): void
    {
        if (isset($configuration['tags'])) {
            $this->addTags($definition, $configuration['tags']);
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function processAutowire(array $configuration, Definition $definition): void
    {
        if (isset($configuration['autowire'])) {
            $this->addAutowired($definition, (bool) $configuration['autowire']);
        }
    }

    /**
     * @param string[][] $setups
     */
    private function addCalls(Definition $definition, array $setups): void
    {
        foreach ($setups as $setterConfiguration) {
            [$methodName, $methodArguments] = $setterConfiguration;
            $methodArguments = $this->prepareMethodArguments($methodArguments);
            $definition->addMethodCall($methodName, $methodArguments);
        }
    }

    /**
     * @param mixed[][] $tags
     */
    private function addTags(Definition $definition, array $tags): void
    {
        foreach ($tags as $tag) {
            if (is_string($tag)) {
                $definition->addTag($tag);
                break;
            }

            $name = key($tag);
            if ($name === 'name') {
                $value = reset($tag);
                $definition->addTag($value);
            } else {
                $definition->addTag($name, $tag);
            }
        }
    }

    private function addAutowired(Definition $definition, bool $isAutowired): void
    {
        $definition->setAutowired($isAutowired);
    }

    /**
     * @param mixed[] $methodArguments
     * @return mixed[]
     */
    private function prepareMethodArguments(array $methodArguments): array
    {
        foreach ($methodArguments as $position => $methodArgument) {
            if (strpos($methodArgument, '@') === 0) {
                $methodArguments[$position] = new Reference(substr($methodArgument, 1));
            }
        }

        return $methodArguments;
    }
}

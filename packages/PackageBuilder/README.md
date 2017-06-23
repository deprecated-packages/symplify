# Package Builder

[![Build Status](https://img.shields.io/travis/Symplify/PackageBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/PackageBuilder)
[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder)

*Write package once and let many other frameworks use it.*

This tools helps you to build package integrations to Symfony and Nette, without any knowledge of their Dependency Injection components.

## Install

```bash
composer require symplify/package-builder
```


## Usage in Nette

```php
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionFinder;

final class SomeExtension extends CompilerExtension
{
    public function beforeCompile(): void
    {
        $eventDispatcherDefinition = DefinitionFinder::getByType($containerBuilder, EventDispatcher::class);
        
        $eventSubscribersDefinitions = DefinitionFinder::findAllByType($containerBuilder, EventSubscriberInterface::class);
        
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            EventDispatcher::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }
}
```


## Usage in Symfony

```php
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionFinder;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;

final class CollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $eventDispatcherDefinition = DefinitionFinder::getByType($containerBuilder, EventDispatcher::class);
        
        $eventSubscribersDefinitions = DefinitionFinder::findAllByType($containerBuilder, EventSubscriberInterface::class);
        
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            EventDispatcher::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }
}
```


That's all :)

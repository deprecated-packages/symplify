# Package Builder

[![Build Status](https://img.shields.io/travis/Symplify/PackageBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/PackageBuilder)
[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder)

*Write package once and let many other frameworks use it.*

This tools helps you to build package integrations to Symfony and Nette, without any knowledge of their Dependency Injection components.

## Install

```bash
composer require symplify/package-builder
```

## Collect Services Together in Extension/Bundle

### In Symfony

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

### In Nette

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


## All Parameters Available in a Service

Note: System parameters are excluded by default.

### In Symfony

Register: 

```yml
# app/config/services.yml

parameters:
    source: src 

services:
    _defaults:
        autowire: true
    
    Symplify\PackageBuilder\Adapter\Symfony\Parameter\ParameterProvider: ~
```

Then require in `__construct()` where needed:

```php
use Symplify\PackageBuilder\Adapter\Symfony\Parameter\ParameterProvider;

final class StatieConfiguration
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    
    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    
    public function getSource(): string
    {
        return $parameterProvider->provide()['source']; // returns "src"
    }
}
```

### In Nette

Register: 

```yml
# app/config/config.neon

parameters:
    source: src 

services:
    - Symplify\PackageBuilder\Adapter\Nette\Parameter\ParameterProvider
```

Then require in `__construct()` where needed:

```php
use Symplify\PackageBuilder\Adapter\Nette\Parameter\ParameterProvider;

final class StatieConfiguration
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    
    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    
    public function getSource(): string
    {
        return $parameterProvider->provide()['source']; // returns "src"
    }
}
```


## Do you need a Vendor Directory?

```php
Symplify\PackageBuilder\Composer\VendorDirProvider::provide(); // return path to vendor directory
```

## Load a Config for CLI Application?

Use in CLI entry file `bin/<app-name>`, e.g. `bin/statie` or `bin/apigen`. 
  
```php
# bin/statie

use Symfony\Component\Console\Input\ArgvInput;

Symplify\PackageBuilder\Configuration\ConfigFilePathHelper::detectFromInput('statie', new ArgvInput);
# throws "Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException" 
# exception if no file is found
```

Where "statie" is key to save the location under. Later you'll use it get the config.  

With `--config` you can set config via CLI.

```bash
bin/statie --config config/statie.neon
```

Then get the config just run:

```php
$config = Symplify\PackageBuilder\Configuration\ConfigFilePathHelper::provide('statie');
dump($config); // returns absolute path to "config/statie.neon"
// or NULL if none was found before
```

You can also provide fallback to file in [current working directory](http://php.net/manual/en/function.getcwd.php):

```php
$config = Symplify\PackageBuilder\Configuration\ConfigFilePathHelper::provide('statie', 'statie.neon');
```

This is common practise in CLI applications, e.g. [PHPUnit](https://phpunit.de/) looks for `phpunit.xml`.


## Use SymfonyStyle for Console Output Anywhere You Need

Another use case for `bin/<app-name>`, when you need to output before building Dependency Injection Container. E.g. when ContainerFactory fails on exception that you need to report nicely.    
 
```php
# bin/statie 

$symfonyStyle = Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::create();
try {
    $containerFactory->create();
} catch (Throwable $throwable) {
    $symfonyStyle->error($throwable->getMessage());
}
```


## Load `*.neon` config files in Kernel
 
You can load `*.yaml` files in Kernel by default. Now `*.neon` as well:
  
```php
namespace Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait;

final class SuperKernel extends Kernel
{
    use NeonLoaderAwareKernelTrait;

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/services.neon');
    }
}
```


That's all :)

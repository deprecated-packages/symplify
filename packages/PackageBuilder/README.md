
# Package Builder

[![Build Status](https://img.shields.io/travis/Symplify/PackageBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/PackageBuilder)
[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fpackage-builder)

This tools helps you with Collectors in DependecyInjection, Console shortcuts, ParameterProvider as service and many more.

## Install

```bash
composer require symplify/package-builder
```

## Use

### Collect Services of Certain Type Together

How do we load Commands to Console Application without tagging?

- Read [What is tagging for](https://www.tomasvotruba.cz/blog/2017/02/12/drop-all-service-tags-in-your-nette-and-symfony-applications/#what-is-tagging-for)
- Read [Collector Pattern, The Shortcut Hack to SOLID Code](https://www.tomasvotruba.cz/clusters/#collector-pattern-the-shortcut-hack-to-solid-code)

```php
<?php declare(strict_types=1);

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;

final class CollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $definitionCollector = new DefinitionCollector(new DefinitionFinder);

        $definitionCollector->loadCollectorWithType(
            $containerBuilder,
            Application::class, // 1 main service
            Command::class, // many collected services
            'add' // the adder method called on 1 main service
        );
    }
}
```

### Add Service by Interface if Found

```php
<?php declare(strict_types=1);

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\PackageBuilder\DependencyInjection\DefinitionFinder;

final class CustomSourceProviderDefinitionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $definitionFinder = new DefinitionFinder();

        $customSourceProviderDefinition = $definitionFinder->getByTypeIfExists(
            $containerBuilder,
            CustomSourceProviderInterface::class
        );

        if ($customSourceProviderDefinition === null) {
            return;
        }

        $sourceFinderDefinition = $definitionFinder->getByType($containerBuilder, SourceFinder::class);
        $sourceFinderDefinition->addMethodCall(
            'setCustomSourceProvider',
            [new Reference($customSourceProviderDefinition->getClass())]
        );
    }
}
```

### Get All Parameters via Service

```yml
# app/config/services.yml

parameters:
    source: "src"

services:
    _defaults:
        autowire: true

    Symplify\PackageBuilder\Parameter\ParameterProvider: ~
```

Then require in `__construct()` where needed:

```php
<?php declare(strict_types=1);

namespace App\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;

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
        return $this->parameterProvider->provideParameter('source'); // returns "src"
    }
}
```

### Get Vendor Directory from Anywhere

```php
<?php declare(strict_types=1);

Symplify\PackageBuilder\Composer\VendorDirProvider::provide(); // returns path to vendor directory
```

### Load a Config for CLI Application?

- Read [How to Load --config With Services in Symfony Console](https://www.tomasvotruba.cz/blog/2018/05/14/how-to-load-config-with-services-in-symfony-console/#code-argvinput-code-to-the-rescue)

Use in CLI entry file `bin/<app-name>`, e.g. `bin/statie` or `bin/apigen`.

```php
<?php declare(strict_types=1);

# bin/statie

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;

ConfigFileFinder::detectFromInput('statie', new ArgvInput);
# throws "Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException" exception if no file is found
```

Where "statie" is key to save the location under. Later you'll use it get the config.

With `--config` you can set config via CLI.

```bash
bin/statie --config config/statie.yml
```

Then get the config just run:

```php
<?php declare(strict_types=1);

$config = Symplify\PackageBuilder\Configuration\ConfigFileFinder::provide('statie');
var_dump($config); // returns absolute path to "config/statie.yml"
// or NULL if none was found before
```

You can also provide fallback to file in [current working directory](http://php.net/manual/en/function.getcwd.php):

```php
<?php declare(strict_types=1);

$config = Symplify\PackageBuilder\Configuration\ConfigFileFinder::provide('statie', ['statie.yml']);
$config = Symplify\PackageBuilder\Configuration\ConfigFileFinder::provide('statie', ['statie.yml', 'statie.yaml']);
```

This is common practise in CLI applications, e.g. [PHPUnit](https://phpunit.de/) looks for `phpunit.xml`.

### Render Fancy CLI Exception Anywhere You Need

Do you get exception before getting into Symfony\Console Application, but still want to render it with `-v`, `-vv`, `-vvv` options?

```php
<?php declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symplify\PackageBuilder\Console\ThrowableRenderer;

require_once __DIR__ . '/autoload.php';

try {
    /** @var Container $container */
    $container = require __DIR__ . '/container.php';

    $application = $container->get(Application::class);
    exit($application->run());
} catch (Throwable $throwable) {
    (new ThrowableRenderer())->render($throwable);
    exit($throwable->getCode());
}
```

### Load Config via `--level` Option in CLI App

In you `bin/your-app` you can use `--level` option as shortcut to load config from `/config` directory.

It makes is easier to load config over traditional super long way:

```bash
vendor/bin/your-app --config vendor/organization-name/package-name/config/subdirectory/the-config.yml
```

```php
<?php declare(strict_types=1);

use App\DependencyInjection\ContainerFactory;
use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Configuration\LevelFileFinder;

// 1. Try --level
$configFile = (new LevelFileFinder)->detectFromInputAndDirectory(new ArgvInput, __DIR__ . '/../config/');

// 2. try --config
if ($configFile === null) {
    ConfigFileFinder::detectFromInput('ecs', new ArgvInput);
    $configFile = ConfigFileFinder::provide('ecs', ['easy-coding-standard.yml']);
}

// 3. Build DI container
$containerFactory = new ContainerFactory; // your own class
if ($configFile) {
    $container = $containerFactory->createWithConfig($configFile);
} else {
    $container = $containerFactory->create();
}
```

And use like this:

```bash
vendor/bin/your-app --level the-config
```

### Merge Parameters in `.yaml` Files Instead of Override?

In Symfony [the last parameter wins by default](https://github.com/symfony/symfony/issues/26713)*, hich is bad if you want to decouple your parameters.

```yaml
# first.yml
parameters:
    another_key:
       - skip_this
```

```yaml
# second.yml
imports:
    - { resource: 'first.yml' }

parameters:
    another_key:
       - skip_that_too
```

The result will change with `Symplify\PackageBuilder\Yaml\FileLoader\ParameterMergingYamlFileLoader`:

```diff
 parameters:
     another_key:
+       - skip_this
        - skip_that_too
```

How to use it?

```php
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Yaml\FileLoader\ParameterMergingYamlFileLoader;

final class AppKernel extends Kernel
{
    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $kernelFileLocator = new FileLocator($this);

        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($container, $kernelFileLocator),
            new ParameterMergingYamlFileLoader($container, $kernelFileLocator)
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
```

In case you need to do more work in YamlFileLoader, just extend the abstract parent `Symplify\PackageBuilder\Yaml\FileLoader\AbstractParameterMergingYamlFileLoader` and add your own logic.

#### Do you Need to Merge YAML files Outside Kernel?

Instead of creating all the classes use this helper class:

```php
<?php declare(strict_types=1);

$parameterMergingYamlLoader = new Symplify\PackageBuilder\Yaml\ParameterMergingYamlLoader;

$parameterBag = $parameterMergingYamlLoader->loadParameterBagFromFile(__DIR__ . '/config.yml');

var_dump($parameterBag);
// instance of "Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface"
```

### Use `%vendor%` and `%cwd%` in Imports Paths

Instead of 2 paths with `ignore_errors` use `%vendor%` and other parameters in imports paths:

```diff
 imports:
-    - { resource: '../../easy-coding-standard/config/psr2.yml', ignore_errors: true }
-    - { resource: 'vendor/symplify/easy-coding-standard/config/psr2.yml', ignore_errors: true }
+    - { resource: '%vendor%/symplify/easy-coding-standard/config/psr2.yml' }
```

You can have that with `Symplify\PackageBuilder\Yaml\FileLoader\ParameterImportsYamlFileLoader`:

```php
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Yaml\FileLoader\ParameterImportsYamlFileLoader;

final class AppKernel extends Kernel
{
    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $kernelFileLocator = new FileLocator($this);

        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($container, $kernelFileLocator),
            new ParameterImportsYamlFileLoader($container, $kernelFileLocator)
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
```

In case you need to do more work in YamlFileLoader, just extend the abstract parent `Symplify\PackageBuilder\Yaml\FileLoader\AbstractParameterImportsYamlFileLoader` and add your own logic.

### Smart Compiler Passes for Lazy Programmers

[How to add compiler pass](https://symfony.com/doc/current/service_container/compiler_passes.html#working-with-compiler-passes-in-bundles)?

#### Autowire Singly-Implemented Interfaces

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass`

```diff
 services:
     OnlyImplementationOfFooInterface: ~
-
-    FooInterface:
-        alias: OnlyImplementationOfFooInterface
```

#### Autobind Parameters

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass`

```diff
 parameters:
     entity_repository_class: 'Doctrine\ORM\EntityRepository'
     entity_manager_class: 'Doctrine\ORM\EntityManager'

 services:
-    _defaults:
-        bind:
-            $entityRepositoryClass: '%entity_repository_class%'
-            $entityManagerClass: '%entity_manager_class%'
-
     Rector\:
         resource: ..
```

#### Use Public Services only in Tests

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass`
- Read [How to Test Private Services in Symfony](https://www.tomasvotruba.cz/blog/2018/05/17/how-to-test-private-services-in-symfony/)

```diff
 # some config for tests
 services:
-    _defaults:
-        public: true
```

That's all :)

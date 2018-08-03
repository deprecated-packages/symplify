
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

### 1. Collect Services of Certain Type Together, Commands to Console Application

```php
<?php declare(strict_types=1);

namespace App\DependencyInjection\CompilerPass;

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
            EventDispatcher::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }
}
```

### 2. Add Service by Interface if Found

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

### 3. Get All Parameters via Service

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

### 4. Get Vendor Directory from Anywhere

```php
Symplify\PackageBuilder\Composer\VendorDirProvider::provide(); // returns path to vendor directory
```

### 5. Load a Config for CLI Application?

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

### 6. Render Fancy CLI Exception Anywhere You Need

Do you get exception before getting into Symfony\Console Application, but still want to render it like the Application would do?
E.g in `bin/<app-name>` when ContainerFactory fails.

Use `Symplify\PackageBuilder\Console\ThrowableRenderer`:

```php
<?php declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symplify\PackageBuilder\Console\ThrowableRenderer;

require_once __DIR__ . '/autoload.php';

// performance boost
gc_disable();

try {
    /** @var Container $container */
    $container = require __DIR__ . '/container.php';

    $application = $container->get(Application::class);
    exit($application->run());
} catch (Throwable $throwable) {
    (new ThrowableRenderer())->render($throwable);
    exit(1);
}
```

### 7. Load Config via `--level` Option in CLI App

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

And use like:

```bash
vendor/bin/your-app --level the-config
```

### 8. Do you need to merge parameters in `.yaml` files instead of override?

Native Symfony approach is *the last wins*, which is bad if you want to decouple your parameters. For more see [the issue](https://github.com/symfony/symfony/issues/26713).

This will be produce with help of `Symplify\PackageBuilder\Yaml\AbstractParameterMergingYamlFileLoader`:

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

The final result will look like this:

```yaml
parameters:
    another_key:
       - skip_this # this one is normally missed
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
use Symplify\PackageBuilder\Yaml\AbstractParameterMergingYamlFileLoader;

final class AppKernel extends Kernel
{
    // ...

    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $kernelFileLocator = new FileLocator($this);

        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($container, $kernelFileLocator),
            // you can 1. create custom YamlFileLoader for other custom tweaks
            // or 2. use short anonymous class like this
            new class($container, $kernelFileLocator) extends AbstractParameterMergingYamlFileLoader {
            },
        ]);

        return new DelegatingLoader($loaderResolver);
    }
}
```

#### Can I Use it Without Kernel?

Do you need to load YAML files elsewhere? Instead of creating all the classes, you can use this helper class:

```php
$parametersMergingYamlLoader = new Symplify\PackageBuilder\Yaml\ParametersMergingYamlLoader;

$parameterBag = $parametersMergingYamlLoader->loadParameterBagFromFile(__DIR__ . '/config.yml');

var_dump($parameterBag);
// instance of "Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface"
```

### 9. Smart Compiler Passes for Lazy Programmers

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

#### Default Default Autowire

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireDefaultCompilerPass`

```diff
 services:
-    _defaults:
-        autowire: true
-
     Symplify\Statie\:
         resource: '../../src'
```

#### Use Public Services Only When Really Need

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicDefaultCompilerPass`

```diff
 services:
-    _defaults:
-        public: true
-
     Symplify\Statie\:
         resource: '../../src'
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

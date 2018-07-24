
# Package Builder

[![Build Status](https://img.shields.io/travis/Symplify/PackageBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/PackageBuilder)
[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fpackage-builder)

This tools helps you with Collectors in DependecyInjection, Console shortcuts, ParameterProvider as service and many more.

## Install

```bash
composer require symplify/package-builder
```

## Usage

### 1. Usage in Symfony CompilerPass

#### Collect Services of Certain Type Together

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

#### Add Service if Found

```php
<?php declare(strict_types=1);

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
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

### 2. All Parameters Available in a Service

Note: System parameters are excluded by default.

Register:

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

### 3. Do you need a Vendor Directory?

```php
Symplify\PackageBuilder\Composer\VendorDirProvider::provide(); // return path to vendor directory
```

### 4. Load a Config for CLI Application?

Use in CLI entry file `bin/<app-name>`, e.g. `bin/statie` or `bin/apigen`.

```php
<?php declare(strict_types=1);

# bin/statie

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;

ConfigFileFinder::detectFromInput('statie', new ArgvInput);
# throws "Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException"
# exception if no file is found
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

### 5. Render Exception to CLI like Application does Anywhere You Need

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

### 6. Load config via `--level` option in your Console Application

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

### 7. Find `vendor/autoload.php` in specific directory for BetterReflection

When you use [BetterReflection](https://github.com/Roave/BetterReflection/) and [`ComposerSourceLocator`](https://github.com/Roave/BetterReflection/blob/master/UPGRADE.md#source-locators-now-require-additional-dependencies), you need to locate non-locator `/vendor/autoload.php`.

```php
$autoloadFile = Symplify\PackageBuilder\Composer\AutoloadFinder::findNearDirectories([
    __DIR__ . '/src'
]);

var_dump($autoloadFile); # contains: __DIR__ . '/vendor`
```

### 8. Autowire Singly-Implemented Interfaces

Just like [this PR to Symfony](https://github.com/symfony/symfony/pull/25282), but also covering cases like:

```yaml
services:
    OnlyImplementationOfFooInterface: ~

    # is this really needed?
    FooInterface:
        alias: OnlyImplementationOfFooInterface
```

Just register `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass` in your `Kernel` instance:

```php
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;

final class AppKernel extends Kernel
{
    // ...

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
```

And then cleanup your configs:

```diff
 services:
     OnlyImplementationOfFooInterface: ~
-
-    FooInterface:
-        alias: OnlyImplementationOfFooInterface
```

### 9. Make services public for tests only

Do you use `$container->get(SomeType::class)` in tests and would you like to avoid this:

```yaml
# app/config/services.yml
services:
    _defaults:
        public: true

    # ...
```

Just add `PublicForTestsCompilerPass` to your Kernel, that will **make services public only for the tests**.

```php
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;

final class AppKernel extends Kernel
{
    // ...

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
    }
}
```

### 10. Do you need to merge parameters in `.yaml` files instead of override?

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

### 11. Are you Tired from Binding Parameters Everywhere?

In Symfony 3.4 [parameters binding](https://symfony.com/blog/new-in-symfony-3-4-local-service-binding) was added. It helps you to prevent writing manually parameters for each particular service. On the other hand, parameters:

- have to be defined in the very same single config
- are bound for that config only, no re-use
- used or it will throw an exception, sorry lazy parameters

**How to solve these obstacles and keep YAML definitions cleaner?**

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

Add `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass` class to your `Kernel`:

```php
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;

final class AppKernel extends Kernel
{
    // ...

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
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

### 12. Do You Use Default Autowiring Everywhere?

Great job! Why to repeat it in every single config?

```diff
 services:
-    _defaults:
-        autowire: true

     Symplify\Statie\:
         resource: '../../src'
```

Just use `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireDefaultCompilerPass`:

```php
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireDefaultCompilerPass;

final class AppKernel extends Kernel
{
    // ...

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireDefaultCompilerPass());
    }
}
```

That's all :)

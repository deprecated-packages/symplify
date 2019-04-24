
# Package Builder

[![Build Status](https://img.shields.io/travis/Symplify/PackageBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/PackageBuilder)
[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder/stats)

This tools helps you with Collectors in DependecyInjection, Console shortcuts, ParameterProvider as service and many more.

## Install

```bash
composer require symplify/package-builder
```

## Use

### Prevent Parameter Typos

Was it `ignoreFiles`? Or `ignored_files`? Or `ignore_file`? Are you lazy to ready every `README.md` to find out the corrent name?
Make developer's live happy by helping them.

```yaml
parameters:
    correctKey: 'value'

services:
    _defaults:
        public: true
        autowire: true

    Symfony\Component\EventDispatcher\EventDispatcher: ~
    # this subscribe will check parameters on every Console and Kernel run
    Symplify\PackageBuilder\EventSubscriber\ParameterTypoProofreaderEventSubscriber: ~

    Symplify\PackageBuilder\Parameter\ParameterTypoProofreader:
        $correctToTypos:
            # correct key name
            correct_key:
                # the most common typos that people make
                - 'correctKey'

                # regexp also works!
                - '#correctKey(s)?#i'
```

This way user is informed on every typo he or she makes via exception:

```bash
Parameter "parameters > correctKey" does not exist.
Use "parameters > correct_key" instead.
```

They can focus less on remembering all the keys and more on programming.

<br>

### Add Service by Interface if Found

```php
<?php declare(strict_types=1);

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
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

<br>

### Get All Parameters via Service

```yml
# app/config/services.yaml

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

<br>

### Get Vendor Directory from Anywhere

```php
<?php declare(strict_types=1);

Symplify\PackageBuilder\Composer\VendorDirProvider::provide(); // returns path to vendor directory
```

<br>

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

<br>

### Load Config via `--level` Option in CLI App

In you `bin/your-app` you can use `--level` option as shortcut to load config from `/config` directory.

It makes is easier to load config over traditional super long way:

```bash
vendor/bin/your-app --config vendor/organization-name/package-name/config/subdirectory/the-config.yml
```

```php
<?php declare(strict_types=1);

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
$appKernel = new AppKernel('prod', true);
if ($configFile) {
    $appKernel->setConfig($configFile);
} else {
}
$appKernel->boot();

$container = $appKernel->getContainer();
```

And use like this:

```bash
vendor/bin/your-app --level the-config
```

<br>

### Merge Parameters in `.yaml` Files Instead of Override?

In Symfony [the last parameter wins by default](https://github.com/symfony/symfony/issues/26713)*, which is bad if you want to decouple your parameters.

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

<br>

### Do you Need to Merge YAML files Outside Kernel?

Instead of creating all the classes use this helper class:

```php
<?php declare(strict_types=1);

$parameterMergingYamlLoader = new Symplify\PackageBuilder\Yaml\ParameterMergingYamlLoader;

$parameterBag = $parameterMergingYamlLoader->loadParameterBagFromFile(__DIR__ . '/config.yml');

var_dump($parameterBag);
// instance of "Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface"
```

<br>

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

<br>

### Smart Compiler Passes for Lazy Programmers ↓

[How to add compiler pass](https://symfony.com/doc/current/service_container/compiler_passes.html#working-with-compiler-passes-in-bundles)?

<br>

### Autowire Singly-Implemented Interfaces

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass`

```diff
 services:
     OnlyImplementationOfFooInterface: ~
-
-    FooInterface:
-        alias: OnlyImplementationOfFooInterface
```

<br>

### Do not Repeat Simple Factories

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass`

This prevent repeating factory definitions for obvious 1-instance factories:

```diff
 services:
     Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory: ~
-     Symfony\Component\Console\Style\SymfonyStyle:
-         factory: ['@Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory', 'create']
```

**How this works?**

The factory class needs to have return type + `create()` method:

```php
<?php

namespace Symplify\PackageBuilder\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

final class SymfonyStyleFactory
{
    public function create(): SymfonyStyle
    {
        // ...
    }
}
```

That's all! The "factory" definition is generated from this obvious usage.

**Put this compiler pass first**, as it creates new definitions that other compiler passes might work with.

### Autowire Array Parameters

- `Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass`

This feature surpasses YAML-defined, tag-based or CompilerPass-based collectors in minimalistic way:

```php
<?php

class Application
{
    /**
     * @var Command[]
     */
    private $commands = [];

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
        var_dump($commands); // instnace of Command collected from all services
    }
}
```

If there are failing cases, just exclude them in constructor:

```php
$this->addCompilerPass(new AutowireArrayParameterCompilerPass([
    'Sonata\CoreBundle\Model\Adapter\AdapterInterface'
]);
```

<br>

### Autobind Parameters

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

<br>

### Always Autowire this Type

Do you want to allow users to register services without worrying about autowiring? After all, they might forget it and that would break their code. Set types to always autowire:

```php
<?php

// ...

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;

// ...

        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([
            FixerInterface::class,
        ]));
```

This will make sure, that `PhpCsFixer\Fixer\FixerInterface` instances are always autowired.

<br>

That's all :)

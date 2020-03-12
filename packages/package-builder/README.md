
# Package Builder

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

### Get All Parameters via Service

```yaml
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
<?php

declare(strict_types=1);

namespace App\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ProjectConfiguration
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
<?php

declare(strict_types=1);

Symplify\PackageBuilder\Composer\VendorDirProvider::provide(); // returns path to vendor directory
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
<?php

declare(strict_types=1);

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
<?php

declare(strict_types=1);

$parameterMergingYamlLoader = new Symplify\PackageBuilder\Yaml\ParameterMergingYamlLoader;

$parameterBag = $parameterMergingYamlLoader->loadParameterBagFromFile(__DIR__ . '/config.yml');

var_dump($parameterBag);
// instance of "Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface"
```

<br>

### Smart Compiler Passes for Lazy Programmers ↓

[How to add compiler pass](https://symfony.com/doc/current/service_container/compiler_passes.html#working-with-compiler-passes-in-bundles)?

<br>

### Do not Repeat Simple Factories

This prevent repeating factory definitions for obvious 1-instance factories:

```diff
 services:
     Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory: ~
-    Symfony\Component\Console\Style\SymfonyStyle:
-        factory: ['@Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory', 'create']
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

**Put this compiler pass first**, as it creates new definitions that other compiler passes might work with:

```php
<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;

final class AppKernel extends Kernel
{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());
        // ...
    }
}
```

<br>

### Always Autowire this Type

Do you want to allow users to register services without worrying about autowiring? After all, they might forget it and that would break their code. Set types to always autowire:

```php
<?php

declare(strict_types=1);

namespace App;

use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;

final class AppKernel extends Kernel
{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(
            new AutowireInterfacesCompilerPass([
                FixerInterface::class,
            ])
        );
    }
}
```

This will make sure, that `PhpCsFixer\Fixer\FixerInterface` instances are always autowired.

<br>

That's all :)

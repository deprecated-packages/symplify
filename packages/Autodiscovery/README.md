# Load Entities, Twig paths and Routes once and for All

[![Build Status](https://img.shields.io/travis/Symplify/Autodiscovery/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Autodiscovery)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/autodiscovery.svg?style=flat-square)](https://packagist.org/packages/symplify/autodiscovery/stats)

For every

- **new Entity namespace**,
- **new Twig path**
- **new Translation catalogue path**
- and **new routes files**,

you need to modify your config. Why do it, when your application can do it for you? Do you autoload each Controller manually? :)

Another feature is YAML convertor - from old pre-Symfony 3.3 to new autodiscovery, autowire and autoconfigure format.

## Install

```bash
composer require symplify/autodiscovery
```

## Usage

### 1. Register Doctrine Annotation Mapping

When you create a new package with entities, you need to register them:

```yaml
# app/config/doctrine.yml
doctrine:
    orm:
        mappings:
            # new set for each new namespace
            ShopsysFrameworkBundle:
                type: annotation
                dir: '%shopsys.framework.root_dir%/src/Model'
                alias: ShopsysFrameworkBundle
                prefix: Shopsys\FrameworkBundle\Model
                is_bundle: false
            # new set for each new namespace
            ShopsysFrameworkBundleComponent:
                type: annotation
                dir: '%shopsys.framework.root_dir%/src/Component'
                alias: ShopsysFrameworkBundleComponent
                prefix: Shopsys\FrameworkBundle\Component
                is_bundle: false
```

It's called [memory lock](https://www.tomasvotruba.cz/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) and it nicely opens doors for "I forgot that..." bugs.

How can we avoid that?

#### With Autodiscovery

```diff
 # app/config/twig.yml
 doctrine:
     orm:
-        mappings:
-            # new set for each new namespace
-            ShopsysFrameworkBundle:
-                type: annotation
-                dir: '%shopsys.framework.root_dir%/src/Model'
-                alias: ShopsysFrameworkBundle
-                prefix: Shopsys\FrameworkBundle\Model
-                is_bundle: false
-            # new set for each new namespace
-            ShopsysFrameworkBundleComponent:
-                type: annotation
-                dir: '%shopsys.framework.root_dir%/src/Component'
-                alias: ShopsysFrameworkBundleComponent
-                prefix: Shopsys\FrameworkBundle\Component
-                is_bundle: false
```

```php
<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Autodiscovery\Discovery;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @var Discovery
     */
    private $discovery;

    public function __construct()
    {
        parent::__construct('dev', true);
        $this->discovery = new Discovery($this->getProjectDir());
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->discovery->discoverEntityMappings($containerBuilder);
    }
}
```

### 2. Twig Paths

When you create a new package with templates, you need to register them:

```yaml
# app/config/twig.yml
twig:
    paths:
        # new line for each new package
        - "%kernel.root_dir%/../package/Product/templates"
        # new line for each new package
        - "%kernel.root_dir%/../package/Social/templates"
```

#### With Autodiscovery

```diff
 # app/config/twig.yml
 twig:
-    paths:
-        - "%kernel.root_dir%/../package/Product/templates/views"
-        - "%kernel.root_dir%/../package/Social/templates/views"
```

```php
<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    // ...

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->discovery->discoverTemplates($containerBuilder);
    }
}
```

### 3. Translation Paths

When you create a new package with translations, you need to register them:

```yaml
# app/config/packages/framework.yml
framework:
    translator:
        paths:
            # new line for each new package
            - "%kernel.root_dir%/../package/Product/translations"
            # new line for each new package
            - "%kernel.root_dir%/../package/Social/translations"
```

#### With Autodiscovery

```diff
 # app/config/packages/framework.yml
 framework:
     translator:
-        paths:
-            - "%kernel.root_dir%/../package/Product/translations"
-            - "%kernel.root_dir%/../package/Social/translations"
```

```php
<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    // ...

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->discovery->discoverTranslations($containerBuilder);
    }
}
```

### 4. Routing

```yaml
# app/config/routes.yaml

# new set for each new package
product_annotations:
    resource: "../packages/Product/src/Controller/"
    type: "annotation"

# new set for each new package
social_annotations:
    resource: "../packages/Social/src/Controller/"
    type: "annotation"
```

#### With Autodiscovery

```diff
 # app/config/routes.yaml

-# new set for each new package
-product_annotations:
-    resource: "../packages/Product/src/Controller/"
-    type: "annotation"
-
-# new set for each new package
-social_annotations:
-    resource: "../packages/Social/src/Controller/"
-    type: "annotation"
```

```php
<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscoverer;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->discovery->discoverRoutes($routeCollectionBuilder);
    }
}
```

This works very well with [local packages](https://www.tomasvotruba.cz/blog/2017/12/25/composer-local-packages-for-dummies/) or [monorepo architecture](https://www.tomasvotruba.cz/clusters/#monorepo-from-zero-to-hero).

## YAML Convertor

```bash
vendor/bin/autodiscovery convert-yaml /src # directory
vendor/bin/autodiscovery convert-yaml config/config.yaml # single file
```

It will convert service definitions in `(config|services).(yml|yaml)` files, to new [Symfony 3.3 DI features described here](https://www.tomasvotruba.cz/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/).

### Configuration

You can configure the namespace depth:

```bash
vendor/bin/autodiscovery convert-yaml config/config.yaml --nesting-level 3 # default: 2
```

Will produce ↓

```yaml
services:
     App\Product\Controller\:
         resource: '../src/Product/Controller'
     App\Product\Repository\:
         resource: '../src/Product/Repository'
```

```bash
vendor/bin/autodiscovery convert-yaml config/config.yaml --nesting-level 1
```

↓

```yaml
services:
     App\:
         resource: '../src'
```

Also, filter by only specific name in services:

```bash
vendor/bin/autodiscovery convert-yaml config/config.yaml --filter Controller
```

This will only dump to resource services, that contains "Controller" string.

<br>

In example code, from this:

```yaml
services:
    some_service:
        class: App\SomeService
        autowire: true

    some_controller:
        class: App\Controller\SomeController
        autowire: true

    first_repository:
        class: App\Repository\FirstRepository
        autowire: true
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    second_repository:
        class: App\Repository\SecondRepository
        autowire: true
        calls:
            - ["setEntityManager", ["@entity_manager"]]

    first_command:
        class: App\Command\FirstCommand
        autowire: true
        tags:
            - { name: console.command }
    second_command:
        class: App\Command\SecondCommand
        autowire: true
        tags:
            - { name: console.command }

    first_subscriber:
        class: App\EventSubscriber\FirstSubscriber
        autowire: true
        tags:
            - { name: kernel.event_subscriber }
    second_subscriber:
        class: App\EventSubscriber\SecondSubscriber
        autowire: true
        tags:
            - { name: kernel.event_subscriber }
```

To this:

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\Repository\FirstRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]
    App\Repository\SecondRepository:
        calls:
            - ["setEntityManager", ["@entity_manager"]]

    App\:
        resource: '../../../src'
```

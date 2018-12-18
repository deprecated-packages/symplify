# Load Entities, Twig paths and Routes once and for All

[![Build Status](https://img.shields.io/travis/Symplify/Autodiscovery/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Autodiscovery)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/autodiscovery.svg?style=flat-square)](https://packagist.org/packages/symplify/autodiscovery/stats)

For every

- **new Entity namespace**,
- **new Twig path**
- and **new routes files**,

you need to modify your config. Why do it, when your application can do it for you? Do you autoload each Controller manually? :)

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
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Autodiscovery\Doctrine\DoctrineEntityMappingAutodiscoverer;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        (new DoctrineEntityMappingAutodiscoverer($containerBuilder))->autodiscover();
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
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Autodiscovery\Twig\TwigPathAutodiscoverer;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        (new TwigPathAutodiscoverer($containerBuilder))->autodiscover();
    }
}
```

### 3. Routing

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
<?php declare(strict_types=1);

namespace App;

use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscover;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();
    }
}
```

This works very well with [local packages](https://www.tomasvotruba.cz/blog/2017/12/25/composer-local-packages-for-dummies/) or [monorepo architecture](https://www.tomasvotruba.cz/clusters/#monorepo-from-zero-to-hero).

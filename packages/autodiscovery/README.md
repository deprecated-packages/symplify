# Load Entities, Twig paths and Routes once and for All

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

It's called [memory lock](https://www.tomasvotruba.com/blog/2018/08/27/why-and-how-to-avoid-the-memory-lock/) and it nicely opens doors for "I forgot that..." bugs.

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
namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
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
namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

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
namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

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
namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->discovery->discoverRoutes($routeCollectionBuilder);
    }
}
```

This works very well with [local packages](https://www.tomasvotruba.com/blog/2017/12/25/composer-local-packages-for-dummies/) or [monorepo architecture](https://www.tomasvotruba.com/clusters/#monorepo-from-zero-to-hero).

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

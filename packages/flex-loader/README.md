# Get Flex Loader code out of Kernel and Keep it Clean

[![Downloads total](https://img.shields.io/packagist/dt/symplify/flex-loader.svg?style=flat-square)](https://packagist.org/packages/symplify/flex-loader/stats)

Keep your Symfony `Kernel` clean from boiler plate code. It easier work with Kernel when you can easily separate your code from *the Flex code*.

## Install

```bash
composer require symplify/flex-loader
```

## Usage

### Traditional Kernel (Before)

```php
namespace App;

use Iterator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @var string
     */
    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): Iterator
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';
        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');

        $loader->load(__DIR__ . '/optional/custom/path' . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir() . '/config';
        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');

        $routes->import(__DIR__ . '/optional/custom/path' . self::CONFIG_EXTS, 'glob');
    }
}
```

### With Flex Loader (After)

```php
namespace App;

use Iterator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\FlexLoader\Flex\FlexLoader;

final class MyProjectKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @var FlexLoader
     */
    private $flexLoader;

    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        $this->flexLoader = new FlexLoader($environment, $this->getProjectDir());
    }

    public function registerBundles(): Iterator
    {
        return $this->flexLoader->loadBundles();
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->flexLoader->loadConfigs($containerBuilder, $loader, [__DIR__ . '/optional/custom/path/']);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->flexLoader->loadRoutes($routeCollectionBuilder, [__DIR__ . '/optional/custom/path/']);
    }
}
```

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).

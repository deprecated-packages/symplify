# Modular Routing

[![Build Status](https://img.shields.io/travis/Symplify/ModularRouting/master.svg?style=flat-square)](https://travis-ci.org/Symplify/ModularRouting)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ModularRouting.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ModularRouting)
[![Downloads](https://img.shields.io/packagist/dt/symplify/modular-routing.svg?style=flat-square)](https://packagist.org/packages/symplify/modular-routing)

To add routes you usually need to add few lines to `app/config/routing.yml`. If you have over dozens of modules, it would be easy to get lost in it. To see all options on how to do that including this package, read [this short article](http://www.tomasvotruba.cz/blog/2016/02/25/modular-routing-in-symfony).


**Thanks to this router, you can add them easily as via service loader**.


## Install

```bash
composer require symplify/modular-routing
```

Add bundle to `AppKernel.php`:

```php
final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Symplify\ModularRouting\SymplifyModularRoutingBundle(),
            // ...
        ];
    }
}
```


## Usage

1. Implement [`RouteCollectionProviderInterface`](src/Contract/Routing/RouteCollectionProviderInterface.php)

    ```php
    use Symfony\Component\Routing\Route;
    use Symfony\Component\Routing\RouteCollection;
    use Symplify\ModularRouting\Contract\Routing\RouteCollectionProviderInterface;
    
    final class SomeRouteCollectionProvider implements RouteCollectionProviderInterface
    {
        public function getRouteCollection() : RouteCollection
        {
            $routeCollection = new RouteCollection();
            $routeCollection->add('my_route', new Route('/hello'));
    
            return $routeCollection;
        }
    }
    ```

2. Register service

    ```yml
    services:
        some_module.route_provider:
            class: SomeModule\Routing\SomeRouteCollectionProvider
            autowire: true # or better use Symplify\DefaultAutowiring package
    ```

That's all!


### Loading YML/XML files

In case you want to load these files, just use [`AbstractRouteCollectionProvider`](src/Routing/AbstractRouteCollectionProvider.php)
with helper methods.

```php
use Symfony\Component\Routing\RouteCollection;
use Symplify\ModularRouting\Routing\AbstractRouteCollectionProvider;

final class FilesRouteCollectionProvider extends AbstractRouteCollectionProvider
{
    public function getRouteCollection(): RouteCollection
    {
        return $this->loadRouteCollectionFromFiles([
            __DIR__ . '/routes.xml',
            __DIR__ . '/routes.yml',
        ]);
        
        // on in case you have only 1 file
        // return $this->loadRouteCollectionFromFile(__DIR__ . '/routes.yml');
    }
}

```


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.

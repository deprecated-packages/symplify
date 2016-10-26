# Default Autowire

[![Build Status](https://img.shields.io/travis/Symplify/DefaultAutowire.svg?style=flat-square)](https://travis-ci.org/Symplify/DefaultAutowire)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/DefaultAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/DefaultAutowire)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/DefaultAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/DefaultAutowire)
[![Downloads](https://img.shields.io/packagist/dt/symplify/default-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/default-autowire)
[![Latest stable](https://img.shields.io/packagist/v/symplify/default-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/default-autowire)

**This bundle turns on autowire for you!**

It turns this:

```yaml
# app/config/config.yml
services:
    price_calculator:
        class: PriceCalculator
        autowire: true

    product_repository:
        class: ProductRepository
        autowire: true

    user_factory:
        class: UserFactory
        autowire: true
```

Into this:

```yaml
# app/config/config.yml
services:
    price_calculator:
        class: PriceCalculator

    product_repository:
        class: ProductRepository

    user_factory:
        class: UserFactory
```

# Install

```bash
composer require symplify/default-autowire
```

Add bundle to `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\DefaultAutowire\SymplifyDefaultAutowireBundle(),
            // ...
        ];
    }
}
```


And that's it!


# Features

## Multiple service of one type? Set preferred one

In case you have multiple connections, entity managers or decorated service,
**you can set default preferred service (name) for each type (class or interface)**.

To make it easier for you, there are few default values. You can change them or add new ones.

```yaml
# app/config/cofing.yml
symplify_default_autowire:
    autowire_types:
        Doctrine\ORM\EntityManager: 'doctrine.orm.default_entity_manager'
        Doctrine\ORM\EntityManagerInterface: 'doctrine.orm.default_entity_manager'
        Doctrine\Portability\Connection: 'database_connection'
        Symfony\Component\EventDispatcher\EventDispatcher: 'event_dispatcher'
        Symfony\Component\EventDispatcher\EventDispatcherInterface: 'event_dispatcher'
        Symfony\Component\Translation\TranslatorInterface: 'translator'
```


# Testing

```bash
vendor/bin/symplify-cs check src tests
vendor/bin/phpunit
```


# Contributing

Rules are simple:

- new feature needs tests
- all tests must pass
- 1 feature per PR

I'd be happy to merge your feature then.

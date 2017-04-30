# Default Autowire

[![Build Status](https://img.shields.io/travis/Symplify/DefaultAutowire/master.svg?style=flat-square)](https://travis-ci.org/Symplify/DefaultAutowire)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/DefaultAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/DefaultAutowire)
[![Downloads](https://img.shields.io/packagist/dt/symplify/default-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/default-autowire)

**This bundle turns on autowire for you!**

It turns this:

```yaml
# app/config/config.yml
services:
    PriceCalculator:
        autowire: true

    ProductRepository:
        autowire: true

    UserFactory:
        autowire: true
```

Into this:

```yaml
# app/config/config.yml
services:
    PriceCalculator: ~
    ProductRepository: ~
    UserFactory: ~
```

# Install

```bash
composer require symplify/default-autowire
```

Add bundle to `AppKernel.php`:

```php
final class AppKernel extends Kernel
{
    public function registerBundles(): array
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


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
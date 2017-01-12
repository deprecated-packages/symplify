# Modular Doctrine Filters

[![Build Status](https://img.shields.io/travis/Symplify/ModularDoctrineFilters.svg?style=flat-square)](https://travis-ci.org/Symplify/ModularDoctrineFilters)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/ModularDoctrineFilters.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ModularDoctrineFilters)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ModularDoctrineFilters.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ModularDoctrineFilters)
[![Downloads](https://img.shields.io/packagist/dt/symplify/modular-doctrine-filters.svg?style=flat-square)](https://packagist.org/packages/symplify/modular-doctrine-filters)
[![Latest stable](https://img.shields.io/packagist/v/symplify/modular-doctrine-filters.svg?style=flat-square)](https://packagist.org/packages/symplify/modular-doctrine-filters)


What are Doctrine Filters? Check [these few slides](https://speakerdeck.com/rosstuck/extending-doctrine-2-for-your-domain-model?slide=15) or see [Usage](#usage) to get the knowledge.


They are present in Doctrine by default. **This package only simplifies their use in modular application and allows passing dependencies to them.**

Why and how? Find your answers in [this short article](http://www.tomasvotruba.cz/blog/2016/04/30/decouple-your-doctrine-filters).



## Install

```bash
composer require symplify/modular-doctrine-filters
```

### Nette

Register extension in `config.neon`:

```yaml
# app/config/config.neon

extensions:
    - Symplify\ModularDoctrineFilters\Adapter\Nette\ModularDoctrineFiltersExtension
```


### Symfony

Add bundle to `AppKernel.php`:

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ModularDoctrineFilters\Adapter\Symfony\ModularDoctrineFiltersBundle,
            // ...
        ];
    }
}
```


## Usage

Create class that implements `Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface`:

```php
use Doctrine\ORM\Mapping\ClassMetadata;
use Symplify\DoctrineFilters\Contract\Filter\FilterInterface;

final class SoftdeletableFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, string $alias) : string
    {
        if ($entity->getReflectionClass()->hasProperty('isDeleted')) {
            return "$alias.isDeleted = 0";
        }

        return '';
    }

}
```

And register as service:


### Nette

```yaml
# app/config/config.neon
services:
    - SoftdeletableFilter
```


### Symfony

```yaml
# Resoureces/config/config.yml
services:
    module.softdeletable_filter:
        class: SoftdeletableFilter
```

*Note: Filters are autowired by default. No need to add dependencies manually.


That's all :)


## Testing

```bash
vendor/bin/symplify-cs check src tests
vendor/bin/phpunit
```


## Contributing

Rules are simple:

- new feature needs tests
- all tests must pass
- 1 feature per PR

I'd be happy to merge your feature then.

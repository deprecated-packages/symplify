# Auto Register Services By Class Name Suffix

Register every Controller, Repository or Command in your app. 

No need to do that manually in `config.yml` files. Very useful in large projects to keep configs clean.
 

[![Build Status](https://img.shields.io/travis/Symplify/ServiceDefinitionDecorator.svg?style=flat-square)](https://travis-ci.org/Symplify/ServiceDefinitionDecorator)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/ServiceDefinitionDecorator.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ServiceDefinitionDecorator)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ServiceDefinitionDecorator.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ServiceDefinitionDecorator)
[![Downloads](https://img.shields.io/packagist/dt/symplify/service-definition-decorator.svg?style=flat-square)](https://packagist.org/packages/symplify/service-definition-decorator)
[![Latest stable](https://img.shields.io/packagist/v/symplify/service-definition-decorator.svg?style=flat-square)](https://packagist.org/packages/symplify/service-definition-decorator)


## Install

```bash
composer require symplify/service-definition-decorator
```

Add bundle to `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ServiceDefinitionDecorator\Symfony\SymplifyAutoServiceR egistrationBundle(),
            // ...
        ];
    }
}
```


## Usage

```yml
# app/config/config.yml
decorator:
    # ...
```

That's all :)


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

We would be happy to merge your feature then.

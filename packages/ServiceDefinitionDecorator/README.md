# Service Definition Decorator

[![Build Status](https://img.shields.io/travis/Symplify/ServiceDefinitionDecorator.svg?style=flat-square)](https://travis-ci.org/Symplify/ServiceDefinitionDecorator)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/ServiceDefinitionDecorator.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ServiceDefinitionDecorator)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ServiceDefinitionDecorator.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ServiceDefinitionDecorator)
[![Downloads](https://img.shields.io/packagist/dt/symplify/service-definition-decorator.svg?style=flat-square)](https://packagist.org/packages/symplify/service-definition-decorator)
[![Latest stable](https://img.shields.io/packagist/v/symplify/service-definition-decorator.svg?style=flat-square)](https://packagist.org/packages/symplify/service-definition-decorator)


**Apply tags, autowire or method setup for specfic class types**. E.g.:
 
- add "console.command" tag to every `Symfony\Component\Console\Command\Command` class
- add "kernel.event_subscriber" for every class, that implements `Symfony\Component\EventDispatcher\EventSubscriberInterface` 
- turn autowire on for all controllers that extends `Symfony\Bundle\FrameworkBundle\Controller\Controller`  


*Inspired by [Nette Decorator](http://www.tomasvotruba.cz/blog/2016/12/24/how-to-avoid-inject-thanks-to-decorator-feature-in-nette/) feature.*


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
            new Symplify\ServiceDefinitionDecorator\Symfony\SymplifyServiceDefinitionDecorator(),
            // ...
        ];
    }
}
```


## Usage

In general, there are 3 feature to use:

```yml
decorator:
    "class/interface type to decorate":
        tags:
            - "tags to apply"
        autowire: true # turn autowiring on
        calls:
            - ["setterMethod", ["argument/service to be set"]] 
```

The most common use cases to start with:

```yml
# app/config/config.yml
decorator:
    Symfony\Component\Console\Command\Command:
        tags:
            - { name: "console.command" }

    Symfony\Component\EventDispatcher\EventSubscriberInterface:
        autowire: true
        tags:
            - { name: "kernel.event_subscriber" }

    Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony\Source\DummyServiceAwareInterface:
        calls:
            - [setDummyService, ['@dummy_service']]
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

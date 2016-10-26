# Controller Autowire

[![Build Status](https://img.shields.io/travis/Symplify/ControllerAutowire.svg?style=flat-square)](https://travis-ci.org/Symplify/ControllerAutowire)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/ControllerAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ControllerAutowire)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ControllerAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ControllerAutowire)
[![Downloads](https://img.shields.io/packagist/dt/symplify/controller-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/controller-autowire)
[![Latest stable](https://img.shields.io/packagist/v/symplify/controller-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/controller-autowire)


This bundle does only 2 things. But does them well:

- **1. registers controllers as services and**
- **2. enables contstructor autowiring for them**


Still wondering **why use controller as services**? Check [this](http://richardmiller.co.uk/2011/04/15/symfony2-controller-as-service) and
[this](http://php-and-symfony.matthiasnoback.nl/2014/06/how-to-create-framework-independent-controllers/) article.

Note: If you look for *controller method autowiring*, [see ActionAutowire bundle](https://github.com/Symplify/ActionAutowire).

## Install

```bash
composer require symplify/controller-autowire
```

Add bundle to `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ControllerAutowire\SymplifyControllerAutowireBundle(),
            // ...
        ];
    }
}
```


## Usage

```php
class SomeController
{
    private $someClass;

    public function __construct(SomeClass $someClass)
    {
        $this->someClass = $someClass;
    }
}
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

I'd be happy to merge your feature then.

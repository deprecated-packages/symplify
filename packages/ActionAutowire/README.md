# Action Autowire

[![Build Status](https://img.shields.io/travis/Symplify/ActionAutowire.svg?style=flat-square)](https://travis-ci.org/Symplify/ActionAutowire)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/ActionAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ActionAutowire)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ActionAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ActionAutowire)
[![Downloads](https://img.shields.io/packagist/dt/symplify/action-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/action-autowire)
[![Latest stable](https://img.shields.io/packagist/v/symplify/action-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/action-autowire)


This bundle **enables action autowiring for controllers**.

Inspired by [Argument Value Resolver](http://symfony.com/doc/current/controller/argument_value_resolver.html) available since Symfony 3.1.

## Install

```bash
composer require symplify/action-autowire
```

Add bundle to `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ActionAutowire\SymplifyActionAutowireBundle(),
            // ...
        ];
    }
}
```


## Usage

```php
class SomeController
{
    public function detailAction(SomeClass $someClass)
    {
        $someClass->someMethod();
        // ...
    }
}
```

This is especially convenient when moving from *named services* to *constructor injection*: 

```php
class SomeController extends Controller
{
    public function detailAction()
    {
        $someClass = $this->get('some_service');
        // ...
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

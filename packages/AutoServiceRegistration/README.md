# Auto Register Services By Class Name Suffix

Register every Controller, Repository or Command in your app. 

No need to do that manually in `config.yml` files. Very useful in large projects to keep configs clean.
 

[![Build Status](https://img.shields.io/travis/Symplify/AutoServiceRegistration.svg?style=flat-square)](https://travis-ci.org/Symplify/AutoServiceRegistration)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/AutoServiceRegistration.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/AutoServiceRegistration)
[![Downloads](https://img.shields.io/packagist/dt/symplify/auto-service-registration.svg?style=flat-square)](https://packagist.org/packages/symplify/auto-service-registration)


## Install

```bash
composer require symplify/auto-service-registration
```

Add bundle to `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\AutoServiceRegistration\Symfony\SymplifyAutoServiceRegistrationBundle(),
            // ...
        ];
    }
}
```


## Usage

```yml
# app/config/config.yml with default value
symplify_auto_service_registration:
    directories_to_scan: # where to scan classes
        - %kernel.root_dir%
        - %kernel.root_dir%/src
    class_suffixes_to_seek: # what class name suffixes to look for
        - Controller
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

# Symplify main repository

[![Build Status](https://img.shields.io/travis/Symplify/Symplify/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Symplify)
[![Coverage Status](https://img.shields.io/coveralls/Symplify/Symplify/master.svg?style=flat-square)](https://coveralls.io/github/Symplify/Symplify?branch=master)


## [Coding Standard](https://github.com/Symplify/CodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)

[Final interface](http://ocramius.github.io/blog/when-to-declare-classes-final/), .. and other non-standard useful Sniffs for CodeSniffer.


## [Easy Coding Standard](https://github.com/Symplify/EasyCodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)

The best and easiest way to start coding standard with. Combined both CodeSniffer and PHP-CS-Fixer


## [Package Builder](https://github.com/Symplify/PackageBuilder)

[![Build Status](https://img.shields.io/travis/Symplify/PackageBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/PackageBuilder)
[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder)

Handy Dependency Injection universal methods for Symfony, Laravel and Nette.

```php
$eventDispatcherDefinition = DefinitionFinder::getByType($containerBuilder, EventDispatcher::class);

$eventSubscribersDefinitions = DefinitionFinder::findAllByType($containerBuilder, EventSubscriberInterface::class);

DefinitionCollector::loadCollectorWithType(
    $containerBuilder,
    EventDispatcher::class,
    EventSubscriberInterface::class,
    'addSubscriber'
);
```


## [Statie](https://github.com/Symplify/Statie)

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/statie.svg?style=flat-square)](https://packagist.org/packages/Symplify/statie)

Statie helps you to host and develop your blog on Github.
A static site generator with aim on community websites.

## [Symbiotic Controller](https://github.com/Symplify/SymbioticController)

[![Build Status](https://img.shields.io/travis/Symplify/SymbioticController/master.svg?style=flat-square)](https://travis-ci.org/Symplify/SymbioticController)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/symbiotic-controller.svg?style=flat-square)](https://packagist.org/packages/Symplify/symbiotic-controller)

Single action and framework-independent presenters with `__invoke()` support in Nette.


## Install

Fork, clone your repository and install dependencies:

```bash
git clone git@github.com:<your-name>/Symplify.git
cd Symplify
composer install
```

## Contributing

This is [monolithic repository](https://www.tomasvotruba.cz/blog/2017/01/31/how-monolithic-repository-in-open-source-saved-my-laziness/) for Symplify packages. Put all your [PRs](https://github.com/Symplify/Symplify/pulls) and [ISSUEs](https://github.com/Symplify/Symplify/issues) here.


### How to Contribute

Just follow 3 rules:

- 1. **new feature needs tests**, bottom limit for coverage 70 % is checked by [coveralls.io](https://coveralls.io/) automatically under every PR
- 2. tests, coding standard and PHPStan **checks must pass**

    ```bash
    composer complete-check
    ```

    Often you don't need to fix coding standard manually, just run:

    ```bash
    composer fix-cs
    ```

We would be happy to merge your feature then.

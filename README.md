<div align="center">
    <img src="/docs/symplify.png?v=3">
</div>

# Symplify - Making Everyday PHP Development Simple

[![Coverage](https://img.shields.io/coveralls/symplify/symplify/master.svg?style=flat-square)](https://coveralls.io/github/symplify/symplify?branch=master)
[![SonarCube](https://img.shields.io/badge/SonarCube_Debt-%3C3-brightgreen.svg?style=flat-square)](https://sonarcloud.io/dashboard?id=symplify_symplify)

In [this monorepo](https://www.tomasvotruba.com/blog/2019/10/28/all-you-always-wanted-to-know-about-monorepo-but-were-afraid-to-ask/) you'll find PHP packages that help you with:

* your **first coding standard**
* **maintenance of monorepo** and changelog
* **clean Kernel** even with Flex loading methods
* **slim and re-usable Symfony configs**

<br>

You'll find all packages in [`/packages`](/packages) directory. Here is a brief overview (tip: click on the package name to see its `README` with more detailed features):

## Coding Standard Utils

### [Easy Coding Standard](https://github.com/symplify/easy-coding-standard)

[![Downloads](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

The easiest way to start a coding standard in your project. Easy, simple and intuitive. Combines both [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

<br>

### [Coding Standard](https://github.com/symplify/coding-standard)

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

[Final interface](http://ocramius.github.io/blog/when-to-declare-classes-final/), [`::class` Constant](https://www.tomasvotruba.com/blog/2017/08/21/5-useful-rules-from-symplify-coding-standard/#3-class-constant-fixer) and other useful Checkers for [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

<br>

## Symfony Kernel and Dependency Injection on MDMA

### [Autodiscovery](https://github.com/symplify/autodiscovery)

[![Downloads](https://img.shields.io/packagist/dt/symplify/autodiscovery.svg?style=flat-square)](https://packagist.org/packages/symplify/autodiscovery/stats)

Forget manual registration of translations, templates, mappings and routes in Symfony Application

<br>

### [Flex Loader](https://github.com/symplify/flex-loader)

[![Downloads](https://img.shields.io/packagist/dt/symplify/flex-loader.svg?style=flat-square)](https://packagist.org/packages/symplify/flex-loader/stats)

Keep your Symfony Kernel slim again and let flex-loader load all the configs

<br>

### [Auto Bind Parameter](https://github.com/symplify/auto-bind-parameter)

[![Downloads](https://img.shields.io/packagist/dt/symplify/auto-bind-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/auto-bind-parameter/stats)

Auto bind parameters for your Symfony applications

<br>

### [Autowire Array Parameter](https://github.com/symplify/autowire-array-parameter)

[![Downloads](https://img.shields.io/packagist/dt/symplify/autowire-array-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/autowire-array-parameter/stats)

Autowire Array Parameters for Symfony applications.

<br>

## Symfony Utils

### [Package Builder](https://github.com/symplify/package-builder)

[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder/stats)

Speed up your package DI Containers integration and Console apps to Symfony

<br>

### [Smart File System](https://github.com/symplify/smart-file-system)

[![Downloads](https://img.shields.io/packagist/dt/symplify/smart-file-system.svg?style=flat-square)](https://packagist.org/packages/symplify/smart-file-system/stats)

Making `SplFileInfo` smarter with methods you really need.

<br>

### [Easy Hydrator](https://github.com/symplify/easy-hydrator)

[![Downloads](https://img.shields.io/packagist/dt/symplify/easy-hydrator.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-hydrator/stats)

Hydrate Arrays to Objects via `__construct` and PHP 7.4!

<br>

## Maintainer's Friend

### [Changelog Linker](https://github.com/symplify/changelog-linker)

[![Downloads](https://img.shields.io/packagist/dt/symplify/changelog-linker.svg?style=flat-square)](https://packagist.org/packages/symplify/changelog-linker/stats)

Why write `CHANGELOG.md`, when you can generate it

<br>

### [Monorepo Builder](https://github.com/symplify/monorepo-builder)

[![Downloads](https://img.shields.io/packagist/dt/symplify/monorepo-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo-builder/stats)

Validate, split, release and maintain Monorepo like a boss

<br>

### [Composer Json Manipulator](https://github.com/symplify/composer-json-manipulator)

[![Downloads](https://img.shields.io/packagist/dt/symplify/composer-json-manipulator.svg?style=flat-square)](https://packagist.org/packages/symplify/composer-json-manipulator/stats)

Manipulate composer.json with Beautiful Object API

<br>

## Static Sites

### [Symfony Static Dumper](https://github.com/symplify/symfony-static-dumper)

[![Downloads](https://img.shields.io/packagist/dt/symplify/symfony-static-dumper.svg?style=flat-square)](https://packagist.org/packages/symplify/changelog-linker/stats)

Dump your Symfony app to HTML + CSS + JS only static website. Useful for deploy to Github Pages and other non-PHP static website hostings.

<br>

## CLI Utils

### [Set Config Resolver](https://github.com/symplify/set-config-resolver)

[![Downloads](https://img.shields.io/packagist/dt/symplify/set-config-resolver.svg?style=flat-square)](https://packagist.org/packages/symplify/set-config-resolver/stats)

Loads configs to you with CLI --config, -c, --set, -s or sets parameter

<br>

## Install

Go to particular package and get it via `composer require`.

## Contributing & Issues

If you have issue and want to improve some package, put it all into this repository.

Fork, clone your repository and install dependencies:

```bash
git clone git@github.com:<your-name>/Symplify.git
cd Symplify
composer update
```

### 3 Steps to Contribute

- **1 feature per pull-request**
- **New feature needs tests**
- Tests and static analysis **must pass**:

    ```bash
    composer complete-check

    # coding standard issues fix with
    composer fix-cs
    ```

We would be happy to merge your feature then.

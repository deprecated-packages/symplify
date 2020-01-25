<div align="center">
    <img src="/docs/symplify.png?v=3">
</div>

# Symplify - Making Everyday PHP Development Simple

[![Coverage](https://img.shields.io/coveralls/symplify/symplify/master.svg?style=flat-square)](https://coveralls.io/github/symplify/symplify?branch=master)
[![SonarCube](https://sonarcloud.io/dashboard?id=symplify_symplify)](https://img.shields.io/badge/SonarCube_Debt-%3C5-brightgreen.svg?style=flat-square)

In [this monorepo](https://gomonorepo.org/) you'll find PHP packages that help you with:

* your **first coding standard**
* **maintenance of monorepo** and changelog
* **clean Kernel** even with Flex loading methods
* **slim and re-usable Symfony configs**

<br>

You'll find all packages in [`/packages`](/packages) directory. Here is a brief overview (tip: click on the package name to see its `README` with more detailed features):

## Coding Standard Utils

### Easy Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

- [symplify/easy-coding-standard](https://github.com/symplify/easy-coding-standard)

The easiest way to start a coding standard in your project. Easy, simple and intuitive. Combines both [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

<br>

### Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

- [symplify/coding-standard](https://github.com/symplify/coding-standard)

[Final interface](http://ocramius.github.io/blog/when-to-declare-classes-final/), [`::class` Constant](https://www.tomasvotruba.com/blog/2017/08/21/5-useful-rules-from-symplify-coding-standard/#3-class-constant-fixer) and other useful Checkers for [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

<br>

## Symfony Kernel and Dependency Injection on MDMA

### Autodiscovery

[![Downloads](https://img.shields.io/packagist/dt/symplify/autodiscovery.svg?style=flat-square)](https://packagist.org/packages/symplify/autodiscovery/stats)

- [symplify/autodiscovery](https://github.com/symplify/autodiscovery)

Forget manual registration of translations, templates, mappings and routes in Symfony Application

<br>

### Flex Loader

[![Downloads](https://img.shields.io/packagist/dt/symplify/flex-loader.svg?style=flat-square)](https://packagist.org/packages/symplify/flex-loader/stats)

- [symplify/flex-loader](https://github.com/symplify/flex-loader)

Keep your Symfony Kernel slim again and let flex-loader load all the configs

<br>

### Auto Bind Parameter

[![Downloads](https://img.shields.io/packagist/dt/symplify/auto-bind-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/auto-bind-parameter/stats)

- [symplify/auto-bind-parameter](https://github.com/symplify/auto-bind-parameter)

Auto bind parameters for your Symfony applications

<br>

### Autowire Array Parameter

[![Downloads](https://img.shields.io/packagist/dt/symplify/autowire-array-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/autowire-array-parameter/stats)

- [symplify/autowire-array-parameter](https://github.com/symplify/autowire-array-parameter)

Autowire Array Parameters for Symfony applications.

<br>

## Symfony Utils

### Package Builder

[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder/stats)

- [symplify/package-builder](https://github.com/symplify/package-builder)

Speed up your package DI Containers integration and Console apps to Symfony

<br>

### Smart File System

[![Downloads](https://img.shields.io/packagist/dt/symplify/smart-file-system.svg?style=flat-square)](https://packagist.org/packages/symplify/smart-file-system/stats)

- [symplify/smart-file-system](https://github.com/symplify/smart-file-system)**

Making `SplFileInfo` smarter with methods you really need.

<br>

## Maintainer's Friend

### Changelog Linker

[![Downloads](https://img.shields.io/packagist/dt/symplify/monorepo-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo-builder/stats)

- [symplify/changelog-linker](https://github.com/symplify/changelog-linker)

Why write `CHANGELOG.md`, when you can generate it

<br>

### Monorepo Builder

[![Downloads](https://img.shields.io/packagist/dt/symplify/changelog-linker.svg?style=flat-square)](https://packagist.org/packages/symplify/changelog-linker/stats)

- [symplify/monorepo-builder](https://github.com/symplify/monorepo-builder)

Validate, split, release and maintain Monorepo like a boss

<br>

### Statie

[![Downloads](https://img.shields.io/packagist/dt/Symplify/statie.svg?style=flat-square)](https://packagist.org/packages/Symplify/statie/stats)

- [symplify/statie](https://github.com/symplify/statie)

[www.statie.org](https://www.statie.org) - Static website generator in PHP with Twig/Latte, that fuels [Pehapkari.cz](https://github.com/pehapkari/pehapkari.cz) and [tomasvotruba.com](https://github.com/tomasvotruba/tomasvotruba.com).

<br>

## CLI Utils

### Set Config Resolver

[![Downloads](https://img.shields.io/packagist/dt/symplify/set-config-resolver.svg?style=flat-square)](https://packagist.org/packages/symplify/set-config-resolver/stats)

- [symplify/set-config-resolver](https://github.com/symplify/set-config-resolver)

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

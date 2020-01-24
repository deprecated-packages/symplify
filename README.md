<div align="center">
    <img src="/docs/symplify.png?v=3">
</div>

# Symplify - Making Everyday PHP Development Simple

[![Coverage](https://img.shields.io/coveralls/symplify/symplify/master.svg?style=flat-square)](https://coveralls.io/github/symplify/symplify?branch=master)

In [this monorepo](https://gomonorepo.org/) you'll find PHP packages that help you with:

* your **first coding standard**
* **maintenance of monorepo** and changelog
* **clean Kernel** even with Flex loading methods
* **slim and re-usable Symfony configs**

<br>

You'll find all packages in [`/packages`](/packages) directory. Here is a brief overview (tip: click on the package name to see its `README` with more detailed features):

## Coding Standard Utils

* **[Easy Coding Standard](https://github.com/symplify/easy-coding-standard)** - The easiest way to start a coding standard in your project. Easy, simple and intuitive. Combines both [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/easy-coding-standard/Code_Checks?style=flat-square)](https://github.com/symplify/easy-coding-standard/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

* **[Coding Standard](https://github.com/symplify/coding-standard)** - [Final interface](http://ocramius.github.io/blog/when-to-declare-classes-final/), [`::class` Constant](https://www.tomasvotruba.cz/blog/2017/08/21/5-useful-rules-from-symplify-coding-standard/#3-class-constant-fixer) and other useful Checkers for [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/coding-standard/Code_Checks?style=flat-square)](https://github.com/symplify/coding-standard/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

## Smarter Symfony Dependency Injection

- **[auto-bind-parameter](https://github.com/symplify/auto-bind-parameter)** - Auto bind parameters for your Symfony applications

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/auto-bind-parameter/Code_Checks?style=flat-square)](https://github.com/symplify/auto-bind-parameter/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/auto-bind-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/auto-bind-parameter/stats)

- **[autowire-array-parameter](https://github.com/symplify/autowire-array-parameter)** - Autowire Array Parameters for Symfony applications

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/autowire-array-parameter/Code_Checks?style=flat-square)](https://github.com/symplify/autowire-array-parameter/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/autowire-array-parameter.svg?style=flat-square)](https://packagist.org/packages/symplify/autowire-array-parameter/stats)

## Slim Symfony Kernel

- **[autodiscovery](https://github.com/symplify/autodiscovery)** - Forget manual registration of translations, templates, mappings and routes in Symfony Application

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/autodiscovery/Code_Checks?style=flat-square)](https://github.com/symplify/autodiscovery/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/autodiscovery.svg?style=flat-square)](https://packagist.org/packages/symplify/autodiscovery/stats)

- **[flex-loader](https://github.com/symplify/flex-loader)** - Keep your Symfony Kernel slim again and let flex-loader load all the configs

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/flex-loader/Code_Checks?style=flat-square)](https://github.com/symplify/flex-loader/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/flex-loader.svg?style=flat-square)](https://packagist.org/packages/symplify/flex-loader/stats)

## Symfony Utils

- **[package-builder](https://github.com/symplify/package-builder)** - Speed up your package DI Containers integration and Console apps to Symfony

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/package-builder/Code_Checks?style=flat-square)](https://github.com/symplify/package-builder/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder/stats)

- **[smart-file-system](https://github.com/symplify/smart-file-system)** - `SplFileInfo` on Steroids

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/smart-file-system/Code_Checks?style=flat-square)](https://github.com/symplify/smart-file-system/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/smart-file-system.svg?style=flat-square)](https://packagist.org/packages/symplify/smart-file-system/stats)

## Maintainer Utils

- **[changelog-linker](https://github.com/symplify/changelog-linker)** - Why write `CHANGELOG.md`, when you can generate it

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/changelog-linker/Code_Checks?style=flat-square)](https://github.com/symplify/changelog-linker/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/changelog-linker.svg?style=flat-square)](https://packagist.org/packages/symplify/changelog-linker/stats)

- **[monorepo-builder](https://github.com/symplify/monorepo-builder)** - Validate, split, release and maintain Monorepo like a boss

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/monorepo-builder/Code_Checks?style=flat-square)](https://github.com/symplify/monorepo-builder/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/monorepo-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo-builder/stats)

- **[statie](https://github.com/symplify/statie)** - [www.statie.org](https://www.statie.org) - Static website generator in PHP with Twig/Latte, that fuels [Pehapkari.cz](https://github.com/pehapkari/pehapkari.cz) and [TomasVotruba.cz](https://github.com/tomasvotruba/tomasvotruba.cz).

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/statie/Code_Checks?style=flat-square)](https://github.com/symplify/statie/actions)
    [![Downloads](https://img.shields.io/packagist/dt/Symplify/statie.svg?style=flat-square)](https://packagist.org/packages/Symplify/statie/stats)

## CLI Utils

- **[set-config-resolver](https://github.com/symplify/set-config-resolver)** - Loads configs to you with CLI --config, -c, --set, -s or sets parameter

    [![Build Status](https://img.shields.io/github/workflow/status/symplify/set-config-resolver/Code_Checks?style=flat-square)](https://github.com/symplify/set-config-resolver/actions)
    [![Downloads](https://img.shields.io/packagist/dt/symplify/set-config-resolver.svg?style=flat-square)](https://packagist.org/packages/symplify/set-config-resolver/stats)

## Migration Utils

- All migration utils were moved to [Migrify](https://github.com/migrify/migrify/) project.

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

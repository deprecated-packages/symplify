# PSR-4 Switcher

[![Downloads total](https://img.shields.io/packagist/dt/symplify/psr4-switcher.svg?style=flat-square)](https://packagist.org/packages/symplify/psr4-switcher/stats)

How to switch to PSR4 in your `composer.json`?

## Install

```bash
composer require symplify/psr4-switcher --dev
```

## Usage

Does short file name matches the class name?

```bash
vendor/bin/psr4-switcher check-file-class-name src
```

What files have 2 and more classes?

```bash
vendor/bin/psr4-switcher find-multi-classes tests
```

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/migrify/migrify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [migrify/migrify](https://github.com/migrify/migrify).

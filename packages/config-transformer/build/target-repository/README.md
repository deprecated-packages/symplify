# Config Format Converter

[![Downloads total](https://img.shields.io/packagist/dt/symplify/config-transformer.svg?style=flat-square)](https://packagist.org/packages/symplify/config-transformer/stats)

Convert Symfony Config Formats From XML/YAML to PHP.

Why to PHP? It's the best format for PHP Symfony applications:

* [10 Cool Features You Get after switching from YAML to PHP Configs](https://tomasvotruba.com/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/)
* [How to Switch from YAML/XML Configs to PHP Today with Symplify](https://tomasvotruba.com/blog/2020/07/27/how-to-switch-from-yaml-xml-configs-to-php-today-with-migrify/)

## Install

```bash
composer require symplify/config-transformer --dev
```

<br>

## Usage

Provide paths to files/dirs you want to convert:

```bash
vendor/bin/config-transformer switch-format config/packages/config-transformer.yaml app/config
```

The input file will be deleted automatically.

## Configuration

With `--target-symfony-version`/`-s` option you specify, what Symfony features should be used (3.2 is used by default).

```bash
vendor/bin/config-transformer switch-format app/config -s 3.3
```

*Note: Symfony YAML parser removes all comments, so be sure to go through files and add still-relevant comments manually.*

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

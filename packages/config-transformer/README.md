# Config Format Converter

[![Downloads total](https://img.shields.io/packagist/dt/symplify/config-transformer.svg?style=flat-square)](https://packagist.org/packages/symplify/config-transformer/stats)

Convert Symfony Config Formats From XML/YAML to PHP

## Install

```bash
composer require symplify/config-transformer --dev
```

<br>

## Convert Config Formats From XML/YAML to PHP

Why? Because YAML beats XML and [PHP beats YAML](https://tomasvotruba.com/blog/2020/07/16/10-cool-features-you-get-after-switching-from-yaml-to-php-configs/).

Provide paths to files/dirs you want to convert:

```bash
vendor/bin/config-transformer switch-format config/packages/ecs.yaml app/config
```

The input file will be deleted automatically.

You can also add `--target-symfony-version`/`-s` to specify, what Symfony features should be used (3.2 is used by default).

```bash
vendor/bin/config-transformer switch-format app/config -s 3.3
```

*Note: Symfony YAML parse removes all comments, so be sure to go through files and add still-relevant comments manually.*

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

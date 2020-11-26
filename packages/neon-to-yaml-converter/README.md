# NEON to YAML Converter

[![Downloads total](https://img.shields.io/packagist/dt/symplify/neon-to-yaml-converter.svg?style=flat-square)](https://packagist.org/packages/symplify/neon-to-yaml-converter/stats)

Do you want to turn your [NEON](https://ne-on.org/) templates to [YAML](https://symfony.com/doc/current/components/yaml.html)? There are [many differences](https://www.tomasvotruba.cz/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/) you need to watch out for.

This tool automates it :)

**Before**

```yaml
includes:
    - another-config.neon

parameters:
    perex: '''
        This is long multiline perex,
that takes too much space.
'''

services:
    - App\SomeService(@anotherService, %perex%)
```

**After**

```yaml
imports:
    - { resource: another-config.yaml }

parameters:
    perex: |
        This is long multiline perex,
        that takes too much space.

services:
    App\SomeService:
        arguments:
            - '@anotherService'
            - '%perex%'
```

And much more!

This package won't do it all for you, but **it will help you with 90 % of the boring work**.

## Install

```bash
composer require symplify/neon-to-yaml-converter --dev
```

## Usage

It scan all the `*.(yml|yaml|neon)` files and converts Neon syntax to Yaml and `*.yaml` file.

```bash
vendor/bin/neon-to-yaml convert file.neon
vendor/bin/neon-to-yaml convert /directory
```

That's it :)

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

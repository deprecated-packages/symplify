# Neon to Yaml Converter

[![Build Status](https://img.shields.io/travis/Symplify/NeonToYamlConverter/master.svg?style=flat-square)](https://travis-ci.org/Symplify/NeonToYamlConverter)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/neon-to-yaml-converter.svg?style=flat-square)](https://packagist.org/packages/symplify/neon-to-yaml-converter/stats)

Do you want to turn your [Neon](https://ne-on.org/) templates to [Yaml](https://symfony.com/doc/current/components/yaml.html)?

**Before**

@todo https://www.tomasvotruba.cz/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/

**After**

@todo

And much more!

This package won't do it all for you, but **it will help you with 80 % of the boring work**.

## Install

```bash
composer require symplify/neon-to-yaml-converter --dev
```

## Usage

It scan all the `*.yaml` files and if it founds Neon syntax in it, it'll convert it to Yaml.

```bash
vendor/bin/neon-to-yaml-converter convert file.neon
vendor/bin/neon-to-yaml-converter convert /directory
```

Do you have all files with `*.neon` suffix? Rename them first:

```bash
vendor/bin/neon-to-yaml-converter rename /directory
```

That's it :)

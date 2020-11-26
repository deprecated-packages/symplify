# Config Class Presence

[![Downloads total](https://img.shields.io/packagist/dt/symplify/class-presence.svg?style=flat-square)](https://packagist.org/packages/symplify/class-presence/stats)

Check NEON/YAML/TWIG/LATTE files for existing classes and class constants

## Install

```bash
composer require symplify/class-presence --dev
```

## Usage

Check configs and templates for non-existing classes.

```bash
vendor/bin/class-presence check src
```

Supported:

- TWIG
- LATTE
- YAML
- NEON
- PHP templates (e.g. Blade)

<br>

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

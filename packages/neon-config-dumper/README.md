# Neon Config Dumper

[![Downloads total](https://img.shields.io/packagist/dt/symplify/neon-config-dumper.svg?style=flat-square)](https://packagist.org/packages/symplify/neon-config-dumper/stats)

Missing PSR-4 autodiscovery for services in NEON for PHPStan.
Do not maintain service registration manually, just dump them to generated neon config with automated command.

## Install

```bash
composer require symplify/neon-config-dumper --dev
```

## Use

To generate config, provide a directory with classes as first arguments, and `--output-file` option path to dump the file into.

```bash
vendor/bin/config-dumper packages/phpstan-rules/src --output-file config/generated-services.neon
```

Tool will generate config with all services, as you'd register them manually. Directories like `ValueObject`, `Contract` etc. are skipped.

<br>

That's all :)

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

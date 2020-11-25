# Switch PHP_CodeSniffer or PHP-CS-Fixer to ECS with Single Command

[![Downloads total](https://img.shields.io/packagist/dt/symplify/sniffer-fixer-to-ecs-converter.svg?style=flat-square)](https://packagist.org/packages/symplify/sniffer-fixer-to-ecs-converter/stats)

- From `phpcs.xml` to `ecs.php`?
- From `.php_cs.dist` to `ecs.php`?

In single command run!

Based on:

- [How to Migrate From PHP CS Fixer to EasyCodingStandard in 6 Steps](https://tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard/)
- [How to Migrate From PHP_CodeSniffer to EasyCodingStandard in 7 Steps](https://tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard/)

## Install

```bash
composer require symplify/sniffer-fixer-to-ecs-converter --dev
```

## Usage

```bash
vendor/bin/sniffer-fixer-to-ecs-converter convert phpcs.xml
```

That's it :)

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

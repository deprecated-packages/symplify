# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of rules for PHPStan used by Symplify projects

- See [Rules Overview](/docs/rules_overview.md)

## Install

```bash
composer require symplify/phpstan-rules --dev
```

Register rules for PHPStan:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/symplify-rules.neon
```

<br>

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).

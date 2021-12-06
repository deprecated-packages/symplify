# PHPStan Latte Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-latte-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-latte-rules/stats)

Rules for static analysis of Latte templates and Latte render() methods

- See [Rules Overview](docs/rules_overview.md)

## Install

```bash
composer require symplify/phpstan-latte-rules --dev
```

## Usage

@todo

## Configuration

[LatteCompleteCheckRule](docs/rules_overview.md) can check usage of all [default latte filters](https://github.com/nette/latte/blob/master/src/Latte/Runtime/Defaults.php#L21). If you use some additional filters, register them in your phpstan.neon as `latteFilters` parameter. Use array `[className, methodName]` for static and dynamic method calls, and simple string for function calls:
```neon
parameters:
    latteFilters:
        someStaticFilter: [SomeFilterClass, processStatic]
        someDynamicFilter: [SomeFilterClass, processDynamic]
        someFunctionFilter: some_function
```

<br>

With application mapping registered in phpstan.neon [LatteCompleteCheckRule](docs/rules_overview.md) can also check if your links are correct:
```neon
parameters:
    presenterFactoryMapping:
        *: App\*Module\*Presenter
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

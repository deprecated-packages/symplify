# PHPStan Rules

[![Downloads](https://img.shields.io/packagist/dt/symplify/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-rules/stats)

Set of rules for PHPStan used by Symplify projects

- See [Rules Overview](docs/rules_overview.md)

## Install

```bash
composer require symplify/phpstan-rules --dev
```

## Add Rules to `phpstan.neon`

Some of rules here require configuration, some not. We recommend to start with rules that do not require any configuration, because there is just one way to use them:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/static-rules.neon
```

Give it couple of days, before extending.

## How we use Configurable Rules

Then there are configurable rules with *saints defaults*. That's just polite wording for *opinionated*, like [`AllowedExclusiveDependencyRule`](https://github.com/symplify/phpstan-rules/blob/master/docs/rules_overview.md#allowedexclusivedependencyrule).

You might not like them, but maybe you do:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/phpstan-rules/config/configurable-rules.neon
```

Give it a trial run... so many erros and unclear feedback.... Would you like to **configure them yourself?**
That's good! We use one rule by another in other projects too, instead of one big import.

- **Pick one and put it to your `phpstan.neon` manually**.
- Configure it to your specific needs and re-run PHPStan. Much better, when you're in control, right?

You'll find them all in [rules overview](docs/rules_overview.md).

Happy coding!

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

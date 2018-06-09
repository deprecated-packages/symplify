# Not only Composer tools to build a Monorepo

[![Build Status](https://img.shields.io/travis/Symplify/MonorepoBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/MonorepoBuilder)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/monorepo-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo-builder)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fmonorepo-builder)

Do you maintain [a monorepo](https://gomonorepo.org/) with more packages?

**This package has few useful tools, that will make that easier**.

## Install

```bash
composer require symplify/monorepo-builder --dev
```

## Usage

### 1. Merge local `composer.json` to the Root One

Merges following sections to the root `composer.json`, so you can only edit `composer.json` of particular packages and let script to synchronize it.

- 'require'
- 'require-dev'
- 'autoload'
- 'autoload-dev'

```php
vendor/bin/monorepo-builder merge
```

### 2. Bump Package Inter-dependencies

Let's say you release `symplify/symplify` 4.0 and you need package to depend on version `^4.0` for each other.

Just run this:

```bash
vendor/bin/monorepo-builder bump-interdependency "^4.0"
```

### 3. Keep Synchronized Package Version

In synchronized monorepo, it's common to use same package version to prevent bugs and WTFs. So if one of your package uses `symfony/console` 3.4 and the other `symfony/console` 4.1, this will tell you:

```bash
vendor/bin/monorepo-builder validate-versions
```

### 4. Keep Package Alias Up-To-Date

You can see this

```json
{
    "extra": {
        "branch-alias": {
            "dev-master": "2.0-dev"
        }
    }
}
```

even if there is already version 3.0 out.

It's super easy to foger this, so this command does this for you:

```bash
vendor/bin/monorepo-builder package-alias
```

## Contributing

Open an [issue](https://github.com/Symplify/Symplify/issues) or send a [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.

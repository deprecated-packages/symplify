# Not only Composer tools to build a Monorepo

[![Build Status](https://img.shields.io/travis/Symplify/MonorepoBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/MonorepoBuilder)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/monorepo-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo-builder/stats)

Do you maintain [a monorepo](https://gomonorepo.org/) with more packages?

**This package has few useful tools, that will make that easier**.

## Install

```bash
composer require symplify/monorepo-builder --dev
```

## Usage

### 0. Are you New to Monorepo?

The best to lean-in fast is to read basic intro at [goMonorepo.com](https://gomonorepo.org/).
We also made a simple command to make that easy for you:

```bash
vendor/bin/monorepo-builder init
```

And the basic setup is done!

### 1. Merge local `composer.json` to the Root One

Merges configured sections to the root `composer.json`, so you can only edit `composer.json` of particular packages and let script to synchronize it.

```yaml
# monorepo-builder.yml
parameters:
    merge_sections:
        # default values
        - 'require'
        - 'require-dev'
        - 'autoload'
        - 'autoload-dev'
        - 'repositories'
```

You can configure it and add `minimum-stablity` for example or remove any of those default values:

```yaml
# monorepo-builder.yml
parameters:
    merge_sections:
        - 'require'
        - 'require-dev'
        - 'minimum-stability'
```

To merge just run:

```bash
vendor/bin/monorepo-builder merge
```

Typical location for packages is `/packages`. But what if you have different naming or extra `/projects` directory?

```yaml
# monorepo-builder.yml
parameters:
    package_directories:
        - 'packages'
        - 'projects'
```

Sections are nicely sorted by saint defaults. Do you want to order them yourself?

```yaml
parameters:
    section_order:
        - 'name'
        - 'autoload'
        - 'autoload-dev'
        - 'require'
        - 'require-dev'
```

#### After Merge Options

Do you need to add or remove some packages only to root `composer.json`?

```yaml
# monorepo-builder.yml
parameters:
    data_to_append:
        autoload-dev:
            psr-4:
                'Symplify\Tests\': 'tests'
        require-dev:
            phpstan/phpstan: '^0.9'

    data_to_remove:
        require:
            'phpunit/phpunit': '*'
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
vendor/bin/monorepo-builder validate
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

Get rid of this manual work! Add this command to your release workflow:

```bash
vendor/bin/monorepo-builder package-alias
```

This will add alias `3.1-dev` to `composer.json` in each package.

If you prefer [`3.1.x-dev`](https://getcomposer.org/doc/articles/aliases.md#branch-alias) over default `3.1-dev`, you can configure it:

```yaml
# monorepo-builder.yml
parameters:
    package_alias_format: '<major>.<minor>.x-dev' # default: "<major>.<minor>-dev"
```

### 5. Split Directories to Git Repositories

Classic use case for monorepo is to synchronize last tag and the `master` branch to allow testing of `@dev` version.

```yaml
# monorepo-builder.yml
parameters:
    directories_to_repositories:
        packages/BetterPhpDocParser: 'git@github.com:Symplify/BetterPhpDocParser.git'
        packages/PackageBuilder: 'git@github.com:Symplify/PackageBuilder.git'
```

And run by:

```bash
vendor/bin/monorepo-builder split
```

To speed up the process about 50-60 %, all repositories are synchronized in parallel.

## Contributing

Open an [issue](https://github.com/Symplify/Symplify/issues) or send a [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.

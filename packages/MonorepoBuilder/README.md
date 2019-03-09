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

Sections are sorted for you by saint defaults. Do you want change the order? Just override `section_order` parameter.

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
            # the line is removed by key, so version is irrelevant, thus *
            'phpunit/phpunit': '*'
```

### 2. Bump Package Inter-dependencies

Let's say you release `symplify/symplify` 4.0 and you need package to depend on version `^4.0` for each other:

```bash
vendor/bin/monorepo-builder bump-interdependency "^4.0"
```

### 3. Keep Synchronized Package Version

In synchronized monorepo, it's common to use same package version to prevent bugs and WTFs. So if one of your package uses `symfony/console` 3.4 and the other `symfony/console` 4.1, this will tell you:

```bash
vendor/bin/monorepo-builder validate
```

### 4. Keep Package Alias Up-To-Date

You can see this even if there is already version 3.0 out:

```json
{
    "extra": {
        "branch-alias": {
            "dev-master": "2.0-dev"
        }
    }
}
```

**Not good.** Get rid of this manual work and add this command to your release workflow:

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
        packages/PackageBuilder: 'git@github.com:Symplify/PackageBuilder.git'
        packages/MonorepoBuilder: 'git@github.com:Symplify/MonorepoBuilder.git'
```

And run by:

```bash
vendor/bin/monorepo-builder split
```

To speed up the process about 50-60 %, all repositories are synchronized in parallel.

### 6. Release Flow

When a new version of your package is released, you have to do many manual steps:

- bump mutual dependencies,
- tag this version,
- `git push` with tag,
- change `CHANGELOG.md` title *Unreleated* to `v<version> - Y-m-d` format
- bump alias and mutual dependency to next version alias

But what if **you forget one or do it in wrong order**? Everything will crash!

The `release` command will make you safe:

```bash
vendor/bin/monorepo-builder release v7.0
```

Are you afraid to tag and push? Use `--dry-run` to see only descriptions:

```bash
vendor/bin/monorepo-builder release v7.0 --dry-run
```

### 7. Set Your Own Release Flow

There is set of few default release workers - classes that implement `Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface`.

You can extend it by adding your own:

```yaml
# monorepo-builder.yml
services:
    App\Release\ShareOnTwitterReleaseWorker: ~
```

And or disable default ones:

```yaml
# monorepo-builder.yml
parameters:
    enable_default_release_workers: false
```

## Contributing

Open an [issue](https://github.com/Symplify/Symplify/issues) or send a [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.

# Not only Composer tools to build a Monorepo

[![Downloads total](https://img.shields.io/packagist/dt/symplify/monorepo-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo-builder/stats)

Do you maintain [a monorepo](https://tomasvotruba.com/blog/2019/10/28/all-you-always-wanted-to-know-about-monorepo-but-were-afraid-to-ask/) with more packages?

**This package has few useful tools, that will make that easier**.

## Install

```bash
composer require symplify/monorepo-builder --dev
```

## Usage

### 0. Are you New to Monorepo?

The best to lean-in fast is to read basic intro at blog post [All You Always Wanted to Know About Monorepo](https://www.tomasvotruba.com/blog/2019/10/28/all-you-always-wanted-to-know-about-monorepo-but-were-afraid-to-ask/#what-is-monorepo).
We also made a simple command to make that easy for you:

```bash
vendor/bin/monorepo-builder init
```

And the basic setup is done!

### 1. Merge local `composer.json` to the Root One

Merges configured sections to the root `composer.json`, so you can only edit `composer.json` of particular packages and let script to synchronize it.

Sections that are needed for monorepo to work will be merged:

- 'require'
- 'require-dev'
- 'autoload'
- 'autoload-dev'
- 'repositories'
- 'extra'

To merge run:

```bash
vendor/bin/monorepo-builder merge
```

<br>

Typical location for packages is `/packages`. But what if you have different naming or extra `/projects` directory?

```php
// File: monorepo-builder.php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // where are the packages located?
    $parameters->set(Option::PACKAGE_DIRECTORIES, [
        // default value
        __DIR__ . '/packages',
        // custom
        __DIR__ . '/projects',
    ]);

    // how skip packages in loaded direectories?
    $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, [__DIR__ . '/packages/secret-package']);

    // "merge" command related

    // what extra parts to add after merge?
    $parameters->set(Option::DATA_TO_APPEND, [
        ComposerJsonSection::AUTOLOAD_DEV => [
            'psr-4' => [
                'Symplify\Tests\\' => 'tests',
            ],
        ],
        ComposerJsonSection::REQUIRE_DEV => [
            'phpstan/phpstan' => '^0.12',
        ],
    ]);

    $parameters->set(Option::DATA_TO_REMOVE, [
        ComposerJsonSection::REQUIRE => [
            // the line is removed by key, so version is irrelevant, thus *
            'phpunit/phpunit' => '*',
        ],
    ]);
};
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

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    // default: "<major>.<minor>-dev"
    $parameters->set(Option::PACKAGE_ALIAS_FORMAT, '<major>.<minor>.x-dev');
};
```

### 5. Split Directories to Git Repositories

Thanks to GitHub Actions, this was never simpler to set up. Use [symplify/github-action-monorepo-split](https://github.com/symplify/github-action-monorepo-split).

How to configure it? See our local setup at [.github/workflows/split_monorepo.yaml](https://github.com/symplify/symplify/blob/main/.github/workflows/split_monorepo.yaml)

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

And add the following release workers to your `monorepo-builder.php`:

```php
// File: monorepo-builder.php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    # release workers - in order to execute
    $services->set(UpdateReplaceReleaseWorker::class);
    $services->set(SetCurrentMutualDependenciesReleaseWorker::class);
    $services->set(AddTagToChangelogReleaseWorker::class);
    $services->set(TagVersionReleaseWorker::class);
    $services->set(PushTagReleaseWorker::class);
    $services->set(SetNextMutualDependenciesReleaseWorker::class);
    $services->set(UpdateBranchAliasReleaseWorker::class);
    $services->set(PushNextDevReleaseWorker::class);
};
```

Are you afraid to tag and push? Use `--dry-run` to see only descriptions:

```bash
vendor/bin/monorepo-builder release v7.0 --dry-run
```

Do you want to release next [patch version](https://semver.org/), e.g. current `v0.7.1` → next `v0.7.2`?

```bash
vendor/bin/monorepo-builder release patch
```

You can use `minor` and `major` too.

### 7. Set Your Own Release Flow

There is set of few default release workers - classes that implement `Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface`.

You need to register them as services. Feel free to start with default ones:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // release workers - in order to execute
    $services->set(Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker::class);
    $services->set(Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker::class);

    // you can extend with your own
    $services->set(App\SendPigeonToTwitterReleaseWorker::class);

    $services->set(Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker::class);
    $services->set(Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker::class);
    $services->set(Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker::class);
    $services->set(Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker::class);
    $services->set(Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker::class);
};
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

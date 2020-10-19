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
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // where are the packages located?
    $parameters->set(Option::PACKAGE_DIRECTORIES, [
        // default vaulue
        __DIR__ . '/packages',
        // custom
        __DIR__ . '/projects',
    ]);

    // how skip packages in loaded direectories?
    $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, [__DIR__ . '/packages/secret-package']);

    // "merge" command related

    // what extra parts to add after merge?
    $parameters->set(Option::DATA_TO_APPEND, [
        'autoload-dev' => [
            'psr-4' => [
                'Symplify\Tests\\' => 'tests',
            ],
        ],
        'require-dev' => [
            'phpstan/phpstan' => '^0.12',
        ],
    ]);

    $parameters->set(Option::DATA_TO_REMOVE, [
        'require' => [
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
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    // default: "<major>.<minor>-dev"
    $parameters->set(Option::PACKAGE_ALIAS_FORMAT, '<major>.<minor>.x-dev');
};
```

### 5. Split Directories to Git Repositories

Classic use case for monorepo is to synchronize last tag and the `master` branch to allow testing of `@dev` version.

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
        __DIR__ . '/packages/package-builder' => 'git@github.com:symplify/package-builder.git',
        __DIR__ . '/packagages/monorepo-builder' => 'git@github.com:symplify/monorepo-builder.git',
        __DIR__ . '/packagages/coding-standard' => 'git@github.com:symplify/coding-standard.git',
    ]);
};
```

Or even simpler:

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
        __DIR__ . '/packages/*' => 'git@github.com:symplify/*.git',
    ]);
};
```

Do you have non standard directory <=> repository name structure?

```bash
/packages/MyFirstPackage => my-first-package.git
```

Add `Option::DIRECTORIES_TO_REPOSITORIES_CONVERT_FORMAT`:

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Split\ValueObject\ConvertFormat;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
        __DIR__ . '/packages/*' => 'git@github.com:symplify/*.git',
    ]);

    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES_CONVERT_FORMAT, ConvertFormat::PASCAL_CASE_TO_KEBAB_CASE);
};
```

<br>

And run by:

```bash
vendor/bin/monorepo-builder split
```

To speed up the process about 50-60 %, all repositories are synchronized in parallel.

#### Testing split locally

If you want to test on local machine, you can set local targets by creating bare repositories:

```bash
mkdir -p [target/path.git]
cd [target/path.git]
git init --bare
#        ^^^^^^ bare!!!
```

Then you can set the target using `file://` prefix for absolute path:

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
        __DIR__ . '/packages/package-builder' => 'file:///home/developer/git/package-builder.git',
        __DIR__ . '/packagages/monorepo-builder' => 'file:///home/developer/git/monorepo-builder.git',
    ]);
};
```

After that you can test the result:

```bash
vendor/bin/monorepo-builder split
cd /tmp
git clone /home/developer/git/package-builder.git
cd package-builder
git log
```

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

Do you want ot release next [patch version](https://semver.org/), e.g. current `v0.7.1` → next `v0.7.2`?

```bash
vendor/bin/monorepo-builder release patch
```

You can use `minor` and `major` too.

### 7. Set Your Own Release Flow

There is set of few default release workers - classes that implement `Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface`.

You need to register them as services. Feel free to start with default ones:

```php
declare(strict_types=1);

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

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).

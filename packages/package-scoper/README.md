# Package Scoper

[![Downloads total](https://img.shields.io/packagist/dt/symplify/package-scoper.svg?style=flat-square)](https://packagist.org/packages/symplify/package-scoper/stats)

- Does your package require Symfony 5, but developers want to use it on Symfony 3?
- Do you want to scope your package dependencies with unique namespace, but don't know how?
- Do you want to skip learning of PhpScoper, PHAR packing, Box and GitHub Actions automated deploy?

You're in the right place!

## Install

```bash
composer require symplify/package-scoper symplify/monorepo-builder --dev
```

We also need `symplify/monorepo-builder`, so we can work with relative paths of local packages.

## Usage

### 1. Generate php-scoper Config

[php-scoper](https://github.com/humbug/php-scoper) is a package that prefixed classes and functions by prefix, so they're unique and don't conflict with same-named class in different version. You can read [the documentation](https://github.com/humbug/php-scoper), or you can generate the config with sain defaults:

```bash
vendor/bin/package-scoper generate-php-scoper
```

It will create `scoper.inc.php` right in the root of the package. That's the best location, because php-scoper works with path relatively to its location.

### 2. Scope Composer Json

Scoping PHP code with [php-scoper](https://github.com/humbug/php-scoper/) is a just first step. The second is making a `composer.json` with different name than the original package. We got you covered! The following command will:

- update package name to `<original>-prefixed`
- keep PHP version in `require` section, license and bin files
- drops the rest

It must be run on the scoped package `composer.json`, not the original one:

```bash
vendor/bin/package-scoper scope-composer-json <path-to-composer-json>

vendor/bin/package-scoper scope-composer-json packages-scoped/some-package/composer.json
```

### 3. Generate GitHub Action Workflow

The process without automatization would not be much helpful. That why we have GitHub Action to automate it.

```bash
vendor/bin/package-scoper generate-workflow
```

Go to your `.github/workflows` folder, update packages names manually and you're ready to go.

### Convention over Configuration in GitHub Action

In the GitHub Workflow you only define package names. To make whole process work, the `<package-name>` must be used in:

1) directory name

```bash
/package/<package-name>
```

2) binary file name:

```bash
/package/<package-name>/bin/<package-name>
```

As the file is defined in the `composer.json`:

```json
{
    "bin": [
        "bin/<package-name>"
    ]
}
```

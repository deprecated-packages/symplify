# Package Scoper

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

```bash
vendor/bin/package-scoper scope-composer-json <path-to-composer-json>
vendor/bin/package-scoper scope-composer-json packages-scoped/some-package/composer.json
```

@todo generate GitHub Action for full publishing

### Generate GitHub Action Workflow

The process without automatization would not be much helpful. That why we have GitHub Action to automate it.

```bash
vendor/bin/package-scoper generate-workflow
```

Go to your `.github/workflows`, update packages names manually and you're ready to go.

### Convention over Configuration in GitHub Action

### Generate PhpScoper Config

[php-scoper](https://github.com/humbug/php-scoper) is a package that prefixed classes and functions by prefix, so they're unique and don't conflict with same-named class in different version.

@todo scoper-php.inc generato

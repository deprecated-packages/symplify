# Package Scoper

- Does your package require Symfony 5, but developers want to use it on Symfony 3?
- Do you want to scope your package dependencies with unique namespace, but don't know how?
- Do you want to skip learning of PhpScoper, PHAR packing, Box and GitHub Actions automated deploy?

You're in the right place!

## Install

```bash
composer require symplify/package-scoper --dev
```

## Usage

```bash
vendor/bin/package-scoper scope-composer-json <path-to-composer-json>
vendor/bin/package-scoper scope-composer-json packages-scoped/some-package/composer.json
```

@todo generate GitHub Action for full publishing

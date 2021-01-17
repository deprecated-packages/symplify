<div align="center">
    <h1>Symplify</h1>
    <img src="/docs/symplify.png?v=3">
    <h2>Making Everyday PHP Development Simple</h2>
</div>

[![Coverage](https://img.shields.io/coveralls/symplify/symplify/master.svg?style=flat-square)](https://coveralls.io/github/symplify/symplify?branch=master)

In [this monorepo](https://www.tomasvotruba.com/blog/2019/10/28/all-you-always-wanted-to-know-about-monorepo-but-were-afraid-to-ask/) you'll find PHP packages that help you with:

* your **first coding standard**
* **maintenance of monorepo** and changelog
* **clean Kernel** even with Flex loading methods
* **slim and re-usable Symfony configs**

<br>

You'll find all packages in [`/packages`](/packages) directory. Here is a brief overview (tip: click on the package name to see its `README` with more detailed features):

## Coding Standards

- [Easy Coding Standard](https://github.com/symplify/easy-coding-standard)
- [Coding Standard](https://github.com/symplify/coding-standard)
- [Easy Coding Standard Tester](https://github.com/symplify/easy-coding-standard-tester)

## For Symfony

- [Autodiscovery](https://github.com/symplify/autodiscovery)
- [Autowire Array Parameter](https://github.com/symplify/autowire-array-parameter)
- [PHP Config Printer](https://github.com/symplify/php-config-printer)
- [Symfony Route Usage](https://github.com/symplify/symfony-route-usage)

## For Package Development

- [Changelog Linker](https://github.com/symplify/changelog-linker)
- [Monorepo Builder](https://github.com/symplify/monorepo-builder)
- [Package Builder](https://github.com/symplify/package-builder)
- [Smart File System](https://github.com/symplify/smart-file-system)
- [Rule Doc Generator](https://github.com/symplify/rule-doc-generator)
- [Skipper](https://github.com/symplify/skipper)
- [Symplify Kernel](https://github.com/symplify/symplify-kernel)
- [Package Scoper](https://github.com/symplify/package-scoper)

## For CLI App Developers

- [Set Config Resolver](https://github.com/symplify/set-config-resolver)
- [Console Color Diff](https://github.com/symplify/console-color-diff)
- [Console Package Builder](https://github.com/symplify/console-package-builder)

## For Any Developer

- [Markdown Diff](https://github.com/symplify/markdown-diff)
- [Easy Hydrator](https://github.com/symplify/easy-hydrator)
- [Easy Testing](https://github.com/symplify/easy-testing)
- [Composer Json Manipulator](https://github.com/symplify/composer-json-manipulator)
- [Symfony Static Dumper](https://github.com/symplify/symfony-static-dumper)

## For PHPStan Lovers

- [PHPStan Rules](https://github.com/symplify/phpstan-rules)
- [PHPStan Extensions](https://github.com/symplify/phpstan-extensions)
- [PHPStan PHP Config](https://github.com/symplify/phpstan-php-config)

## For CI Keeping you Safe

- [Easy CI](https://github.com/symplify/easy-ci)
- [Class Presence](https://github.com/symplify/class-presence)
- [Template Checker](https://github.com/symplify/template-checker)
- [Static Detector](https://github.com/symplify/static-detector)

## For Syntax Transformation

- [Latte to TWIG Converter](https://github.com/symplify/latte-to-twig-converter)
- [NEON to YAML Converter](https://github.com/symplify/neon-to-yaml-converter)
- [Sniffer Fixer to ECS Converter](https://github.com/symplify/sniffer-fixer-to-ecs-converter)

## For Upgrades

- [PHPUnit Upgrader](https://github.com/symplify/phpunit-upgrader)
- [PSR-4 Switcher](https://github.com/symplify/psr4-switcher)
- [Vendor Patches](https://github.com/symplify/vendor-patches)

<br>

## Contributing & Issues

If you have issue and want to improve some package, put it all into this repository.

Fork and clone your repository:

```bash
git clone git@github.com:<your-name>/symplify.git
cd symplify
composer install
```

### 3 Steps to Contribute

- **1 feature per pull-request**
- **new feature must have tests**
- tests and static analysis **must pass**:

    ```bash
    vendor/bin/phpunit
    composer phpstan
    ```

We would be happy to merge your feature then :+1:

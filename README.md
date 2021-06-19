<div align="center">
    <br>
    <img src="/docs/zen.jpg?v=3">
    <br>
    <h1>Symplify - Making Everyday PHP Development Simple</h1>
</div>

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
- [Simple PHP Doc Parser](https://github.com/symplify/simple-php-doc-parser)

## For Symfony

- [Amnesia](https://github.com/symplify/amnesia)
- [Autowire Array Parameter](https://github.com/symplify/autowire-array-parameter)
- [PHP Config Printer](https://github.com/symplify/php-config-printer)

## For Package Development

- [Monorepo Builder](https://github.com/symplify/monorepo-builder)
- [Package Builder](https://github.com/symplify/package-builder)
- [Smart File System](https://github.com/symplify/smart-file-system)
- [Rule Doc Generator](https://github.com/symplify/rule-doc-generator)
- [Skipper](https://github.com/symplify/skipper)
- [Symplify Kernel](https://github.com/symplify/symplify-kernel)

## For CLI App Developers

- [Console Color Diff](https://github.com/symplify/console-color-diff)
- [Console Package Builder](https://github.com/symplify/console-package-builder)

## For Any Developer

- [Git Wrapper](https://github.com/symplify/git-wrapper)
- [Markdown Diff](https://github.com/symplify/markdown-diff)
- [Easy Hydrator](https://github.com/symplify/easy-hydrator)
- [Easy Testing](https://github.com/symplify/easy-testing)
- [Composer Json Manipulator](https://github.com/symplify/composer-json-manipulator)
- [Symfony Static Dumper](https://github.com/symplify/symfony-static-dumper)

## For PHPStan Lovers

- [Astral](https://github.com/symplify/astral)
- [PHPStan Rules](https://github.com/symplify/phpstan-rules)
- [PHPStan Extensions](https://github.com/symplify/phpstan-extensions)

## For CI Keeping you Safe

- [Easy CI](https://github.com/symplify/easy-ci)
- [Class Presence](https://github.com/symplify/class-presence)
- [Static Detector](https://github.com/symplify/static-detector)

## For Syntax Transformation

- [Config Transformer](https://github.com/symplify/config-transformer)
- [Latte to TWIG Converter](https://github.com/symplify/latte-to-twig-converter)
- [NEON to YAML Converter](https://github.com/symplify/neon-to-yaml-converter)

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

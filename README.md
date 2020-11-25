<div align="center">
    <h1>Symplify</h1>
    <img src="/docs/symplify.png?v=3">
    <h2>Making Everyday PHP Development Simple</h2>
</div>

[![Coverage](https://img.shields.io/coveralls/symplify/symplify/master.svg?style=flat-square)](https://coveralls.io/github/symplify/symplify?branch=master)
[![SonarCube](https://img.shields.io/badge/SonarCube_Debt-%3C3-brightgreen.svg?style=flat-square)](https://sonarcloud.io/dashboard?id=symplify_symplify)

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
- [Flex Loader](https://github.com/symplify/flex-loader)
- [PHP Config Printer](https://github.com/symplify/php-config-printer)

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

## For Any Developer

- [Markdown Diff](https://github.com/symplify/markdown-diff)
- [Easy Hydrator](https://github.com/symplify/easy-hydrator)
- [Easy Testing](https://github.com/symplify/easy-testing)
- [Composer Json Manipulator](https://github.com/symplify/composer-json-manipulator)
- [Symfony Static Dumper](https://github.com/symplify/symfony-static-dumper)

## For PHPStan Lovers

- [PHPStan Rules](https://github.com/symplify/phpstan-rules)
- [PHPStan Extensions](https://github.com/symplify/phpstan-extensions)

<br>

## Contributing & Issues

If you have issue and want to improve some package, put it all into this repository.

Fork, clone your repository and install dependencies:

```bash
git clone git@github.com:<your-name>/symplify.git
cd Symplify
composer update
```

### 3 Steps to Contribute

- **1 feature per pull-request**
- **New feature needs tests**
- Tests and static analysis **must pass**:

    ```bash
    composer complete-check

    # coding standard issues fix with
    composer fix-cs
    ```

We would be happy to merge your feature then.

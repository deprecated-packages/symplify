<div align="center">
    <h1>Symplify</h1>
    <a href="https://travis-ci.org/Symplify/Symplify">
        <img src="https://img.shields.io/travis/Symplify/Symplify/master.svg?style=flat-square" alt="CI Status">
    </a>
    <a href="https://coveralls.io/github/Symplify/Symplify?branch=master">
        <img src="https://img.shields.io/coveralls/Symplify/Symplify/master.svg?style=flat-square" alt="Coverage Status">
    </a>
</div>

<br>

## [Easy Coding Standard](https://github.com/Symplify/EasyCodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

The best and easiest way to start coding standard with. Combined both [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

![ECS-Run](packages/EasyCodingStandard/docs/run-and-fix.gif)

**Used by:**

<p align="center">
    <a href="https://github.com/lmc-eu/php-coding-standard"><img src="/packages/EasyCodingStandard/docs/logos/lmc.png"></a>
    <img src="/packages/EasyCodingStandard/docs/logos/space.png">
    <a href="https://github.com/nette/coding-standard"><img src="/packages/EasyCodingStandard/docs/logos/nette.png"></a>
    <img src="/packages/EasyCodingStandard/docs/logos/space.png">
    <a href="https://github.com/php-ai/php-ml/"><img src="/packages/EasyCodingStandard/docs/logos/phpai.png"></a>
    <br>
    <br>
    <a href="https://github.com/shopsys/coding-standards"><img src="/packages/EasyCodingStandard/docs/logos/shopsys.png"></a>
    <img src="/packages/EasyCodingStandard/docs/logos/space.png">
    <a href="https://github.com/sunfoxcz/coding-standard"><img src="/packages/EasyCodingStandard/docs/logos/sunfox.jpg"></a>
    <img src="/packages/EasyCodingStandard/docs/logos/space.png">
    <a href="https://github.com/SyliusLabs/CodingStandard"><img src="/packages/EasyCodingStandard/docs/logos/sylius.png"></a>
</p>

<br>

## [Coding Standard](https://github.com/Symplify/CodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

[Final interface](http://ocramius.github.io/blog/when-to-declare-classes-final/), [`::class` Constant](https://www.tomasvotruba.cz/blog/2017/08/21/5-useful-rules-from-symplify-coding-standard/#3-class-constant-fixer) and other useful Checkers for [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

<br>

## [Statie](https://github.com/Symplify/Statie) - [www.statie.org](https://www.statie.org)

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/statie.svg?style=flat-square)](https://packagist.org/packages/Symplify/statie/stats)

Statie helps you to host and develop your blog on Github.
A static site generator with aim on community websites.

[Pehapkari.cz](https://pehapkari.cz/) ([Github repo](https://github.com/pehapkari/pehapkari.cz)) and [TomasVotruba.cz](https://www.tomasvotruba.cz/) ([Github repo](https://github.com/tomasvotruba/tomasvotruba.cz)).

<br>

### Other Utils Packages

You'll find them all in [`/packages`](/packages) directory:

- [Autodiscovery](https://github.com/Symplify/Autodiscovery) - Forget manual registration of translations, templates, mappings and routes
- [ChangelogLinker](https://github.com/Symplify/ChangelogLinker) - Why write `CHANGELOG.md` if you can generate it
- [EasyCodingStandardTester](https://github.com/Symplify/EasyCodingStandardTester) - The Best Way to Test Sniffs and Fixers
- [FlexLoader](https://github.com/Symplify/FlexLoader) - Keep your Kernel slim again and let FlexLoader load all the configs
- [LatteToTwigConverter](https://github.com/Symplify/LatteToTwigConverter) - Converts Latte templates to Twig
- [MonorepoBuilder](https://github.com/Symplify/MonorepoBuilder) - Validate, split, release and maintain monorepo like a boss
- [PackageBuilder](https://github.com/Symplify/PackageBuilder) - Speed up your package DI Containers integration and Console apps to Symfony

## Install

Fork, clone your repository and install dependencies:

```bash
git clone git@github.com:<your-name>/Symplify.git
cd Symplify
composer update
```

## Contributing

This is a [monolithic repository](https://gomonorepo.org/) for Symplify packages. Put all your [PRs](https://github.com/Symplify/Symplify/pulls) and [ISSUEs](https://github.com/Symplify/Symplify/issues) here.

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

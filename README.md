# Symplify main repository

[![Build Status](https://img.shields.io/travis/Symplify/Symplify/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Symplify)
[![Coverage Status](https://img.shields.io/coveralls/Symplify/Symplify/master.svg?style=flat-square)](https://coveralls.io/github/Symplify/Symplify?branch=master)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fsymplify)

## [Coding Standard](https://github.com/Symplify/CodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)

[Final interface](http://ocramius.github.io/blog/when-to-declare-classes-final/), [`::class` Constant](https://www.tomasvotruba.cz/blog/2017/08/21/5-useful-rules-from-symplify-coding-standard/#3-class-constant-fixer) and other useful Checkers for [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

## [Easy Coding Standard](https://github.com/Symplify/EasyCodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)

The best and easiest way to start coding standard with. Combined both [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

**Used by [Shopsys](https://github.com/shopsys/coding-standards), [Nette](https://github.com/nette/coding-standard) and [Sylius](https://github.com/SyliusLabs/CodingStandard).**

## [Statie](https://github.com/Symplify/Statie) - [www.statie.org](https://www.statie.org)

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/statie.svg?style=flat-square)](https://packagist.org/packages/Symplify/statie)

Statie helps you to host and develop your blog on Github.
A static site generator with aim on community websites.

[Pehapkari.cz](https://pehapkari.cz/) ([Github repo](https://github.com/pehapkari/pehapkari.cz)) and [TomasVotruba.cz](https://www.tomasvotruba.cz/) ([Github repo](https://github.com/tomasvotruba/tomasvotruba.cz)).

## [TokenRunner](https://github.com/Symplify/TokenRunner)

[![Build Status](https://img.shields.io/travis/Symplify/TokenRunner/master.svg?style=flat-square)](https://travis-ci.org/Symplify/TokenRunner)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/token-runner.svg?style=flat-square)](https://packagist.org/packages/Symplify/token-runner)

Building own Sniffs and Fixers made easy. It helps you with:

- Space counts
- Testing
- (Fully qualified) names
- Property and argument types
- And much more...

## Install

Fork, clone your repository and install dependencies:

```bash
git clone git@github.com:<your-name>/Symplify.git
cd Symplify
composer install
```

## Contributing

This is a [monolithic repository](https://gomonorepo.org/) for Symplify packages. Put all your [PRs](https://github.com/Symplify/Symplify/pulls) and [ISSUEs](https://github.com/Symplify/Symplify/issues) here.

### 3 Steps to Contribute

- **1 feature per pull-request**
- **New feature needs tests**
- Tests and static analysis **must pass**:
 
    ```bash
    composer complete-check

    # coding standard issues? fix with
    composer fix-cs
    ```

We would be happy to merge your feature then.

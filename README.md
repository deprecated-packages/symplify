# Symplify main repository

[![Build Status](https://img.shields.io/travis/Symplify/Symplify/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Symplify)
[![Coverage Status](https://img.shields.io/coveralls/Symplify/Symplify/master.svg?style=flat-square)](https://coveralls.io/github/Symplify/Symplify?branch=master)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fsymplify)



## [Coding Standard](https://github.com/Symplify/CodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fcoding-standard)


[Final interface](http://ocramius.github.io/blog/when-to-declare-classes-final/), [`::class` Constant](https://www.tomasvotruba.cz/blog/2017/08/21/5-useful-rules-from-symplify-coding-standard/#3-class-constant-fixer), [Equal Interface](https://www.tomasvotruba.cz/blog/2017/08/21/5-useful-rules-from-symplify-coding-standard/#5-equal-interface) and other useful Checkers for [PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer) and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).


## [Easy Coding Standard](https://github.com/Symplify/EasyCodingStandard)

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fcoding-standard)

The best and easiest way to start coding standard with. Combined both CodeSniffer and PHP-CS-Fixer

**Used by [Nette](https://github.com/nette/coding-standard) and [Sylius](https://github.com/SyliusLabs/CodingStandard).**


## [TokenRunner](https://github.com/Symplify/TokenRunner)

[![Build Status](https://img.shields.io/travis/Symplify/TokenRunner/master.svg?style=flat-square)](https://travis-ci.org/Symplify/TokenRunner)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/token-runner.svg?style=flat-square)](https://packagist.org/packages/Symplify/token-runner)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Ftoken-runner)


@todo

## [Statie](https://github.com/Symplify/Statie)

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/Symplify/statie.svg?style=flat-square)](https://packagist.org/packages/Symplify/statie)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fstatie)

Statie helps you to host and develop your blog on Github.
A static site generator with aim on community websites.

[Pehapkari.cz](https://pehapkari.cz/) ([Github repo](https://github.com/pehapkari/pehapkari.cz)) and [TomasVotruba.cz](https://www.tomasvotruba.cz/) ([Github repo](https://github.com/tomasvotruba/tomasvotruba.cz)).


## Install

Fork, clone your repository and install dependencies:

```bash
git clone git@github.com:<your-name>/Symplify.git
cd Symplify
composer install
```

## Contributing

This is [monolithic repository](https://www.tomasvotruba.cz/blog/2017/01/31/how-monolithic-repository-in-open-source-saved-my-laziness/) for Symplify packages. Put all your [PRs](https://github.com/Symplify/Symplify/pulls) and [ISSUEs](https://github.com/Symplify/Symplify/issues) here.


### How to Contribute

Just follow 3 rules:

- **1 feature per pull-request**
- **New feature needs tests**. [Coveralls.io](https://coveralls.io/) checks code coverage under every PR.
- Tests, coding standard and PHPStan **checks must pass**

    ```bash
    composer complete-check
    ```

    Often you don't need to fix coding standard manually, just run:

    ```bash
    composer fix-cs
    ```

We would be happy to merge your feature then.

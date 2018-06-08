# Not only Composer tools to build a Monorepo

[![Build Status](https://img.shields.io/travis/Symplify/MonorepoBuilder/master.svg?style=flat-square)](https://travis-ci.org/Symplify/MonorepoBuilder)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard-tester.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard-tester)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Feasy-coding-standard-tester)

Do you maintain [a monorepo](https://gomonorepo.org/) with more packages?

**This package has few useful tools, that will make that easier**.

## Install

```bash
composer require symplify/monorepo-builder --dev
```

## Usage

### 1. Merge local `composer.json` to the Root One

```php
vendor/bin/monorepo-builder merge
```

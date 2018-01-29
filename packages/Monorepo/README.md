# Monorepo - Build and Maintain Monorepo like a Boss

[![Build Status](https://img.shields.io/travis/Symplify/Monorepo/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Monorepo)
[![Downloads](https://img.shields.io/packagist/dt/symplify/monorepo.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fmonorepo)


## Install

```php
composer require symplify/monorepo
```

## Usage

### 3 Steps to Build Monolitic Repository from Many Repositories


1. Add `monorepo.yml` with `build` section

```yml
parameters:
    build:
        # link to remote git repository => local subdirectory in monorepo 
        'git@github.com:shopsys/product-feed-zbozi.git': 'packages/ProductFeedZbozi'
        'git@github.com:shopsys/product-feed-heureka.git': 'packages/ProductFeedHeureka'
```

2. Prepare empty directory where, do you want to create your monorepo, e.g. `new-monorepo`. It should be outside current working directory.

3. Run `build` command with path as argument.

```bash
vendor/bin/monorepo build ../new-monorepo 
```

And that's it.

Now your packages will be in:

```bash
/new-monorepo
    /packages
        /ProductFeedZbozi
        /ProductFeedHeureka
```

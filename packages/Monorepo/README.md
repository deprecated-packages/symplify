# Monorepo - Build and Maintain Monorepo like a Boss

[![Build Status](https://img.shields.io/travis/Symplify/Monorepo/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Monorepo)
[![Downloads](https://img.shields.io/packagist/dt/symplify/monorepo.svg?style=flat-square)](https://packagist.org/packages/symplify/monorepo)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fmonorepo)

## Requirements

- [dflydev/git-subsplit](https://github.com/dflydev/git-subsplit) for `git subsplit` command

## Usage

### Split Monolithic Repository to Many Repositories

1. Create `monorepo.yml` with `split` section

```yml
parameters:
    split:
        directory in monorepo with package => remote git repository
        'packages/ProductFeedZbozi': 'git@github.com:shopsys/product-feed-zbozi.git'
        'packages/ProductFeedHeureka': 'git@github.com:shopsys/product-feed-heureka.git'
```

2. Run `split` command

```bash
vendor/bin/monorepo split
```

Your last tag and `master` branch is now published in the repository.

### Build Monolithic Repository from Many Repositories

- Do you have **many packages with long git history**?
- Do you want to **turn them into monorepo**?
- Do you want **keep their history**?

That's exactly what `build` command does.

#### Directories to work With

You're working with 2 directories:

- **monorepo directory** - monorepo will be created there, it must be empty
- **build directory** - where you have `symplify/monorepo` installed, e.g.

    ```bash
    composer require symplify/monorepo
    ```

Do all following steps in **build directory**.

#### 3 Steps to Build Monorepo

1. Create `monorepo.yml` with `build` section

```yml
parameters:
    build:
        # remote git repository => directory in monorepo to place the package to
        'git@github.com:shopsys/product-feed-zbozi.git': 'packages/ProductFeedZbozi'
        'git@github.com:shopsys/product-feed-heureka.git': 'packages/ProductFeedHeureka'
```

2. Run `build` command with **monorepo directory** as argument

Remember, it must be outside this directory and must be empty.

```bash
vendor/bin/monorepo build ../new-monorepo
```

3. A new `/new-monorepo` directory is created, with git history for all the packages

```bash
/new-monorepo
    /packages
        /ProductFeedZbozi
        /ProductFeedHeureka
```

# Symplify main repository

This is Symplify [monorepo](https://www.tomasvotruba.cz/blog/2017/01/31/how-monolithic-repository-in-open-source-saved-my-laziness/). Please put all your PRs and ISSUEs HERE.

[![Build Status](https://img.shields.io/travis/Symplify/Symplify/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Symplify)
[![Coverage Status](https://img.shields.io/coveralls/Symplify/Symplify/master.svg?style=flat-square)](https://coveralls.io/github/Symplify/Symplify?branch=master)


## Install

Fork it and clone your repository:

```bash
git clone git@github.com:<your-name>/Symplify.git
```

## Contributing

Rules are simple:

- new feature needs tests
- all tests and checks must pass

    ```bash
    composer complete-check
    ```
    
- fix coding standard    
    
    ```bash
    composer fix-cs
    ```
    
- 1 feature per PR

We would be happy to merge your feature then.

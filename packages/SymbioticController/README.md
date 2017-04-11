# Symbiotic Controller

[![Build Status](https://img.shields.io/travis/Symplify/SymbioticController.svg?style=flat-square)](https://travis-ci.org/Symplify/SymbioticController)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/SymbioticController.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/SymbioticController)
[![Downloads](https://img.shields.io/packagist/dt/symplify/symbiotic-controller.svg?style=flat-square)](https://packagist.org/packages/symplify/symbiotic-controller)

*Write controller once and let many other frameworks use it.*

## Install

```bash
composer require symplify/symbiotic-controller
```


## Usage in Nette

### Register Extensions to you App

```yaml
# app/config/config.neon

extensions:
    - Symplify\SymbioticController\Adapter\Nette\DI\SymbioticControllerExtension
    - Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension
```

@todo - symbiotic controller


That's all :)

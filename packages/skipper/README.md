# Skipper

Skip files by rule class, fnmatch or regex.

[![Downloads total](https://img.shields.io/packagist/dt/symplify/skipper.svg?style=flat-square)](https://packagist.org/packages/symplify/skipper/stats)

## Install

```bash
composer require symplify/skipper
```

Register bundle in your Kernel:

```php
declare(strict_types=1);

namespace App;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class AppKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new SkipperBundle()];
    }
}
```

## Use

### 1. Configure with `Option::SKIP` parameter.

@todo

### 2. Configure with `Option::ONLY` parameter.

@todo

### 3. Use `Skipper` service

@todo

@todo test on windows in Github Actions - paths!!!!

```php
isFileSkipped()
```

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).

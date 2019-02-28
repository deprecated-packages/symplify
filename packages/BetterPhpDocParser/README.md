# [Deprecated, moved to Rector](https://github.com/rectorphp/rector)

# Better PhpDoc Parser 

[![Build Status](https://img.shields.io/travis/Symplify/BetterPhpDocParser/master.svg?style=flat-square)](https://travis-ci.org/Symplify/BetterPhpDocParser)
[![Downloads](https://img.shields.io/packagist/dt/symplify/better-phpdoc-parser.svg?style=flat-square)](https://packagist.org/packages/symplify/better-phpdoc-parser/stats)

Wrapper around [phpstan/phpdoc-parser](https://github.com/phpstan/phpdoc-parser) that adds **format preserving printer**.

## When do We Need Format Preserving Printer?

**Original code**

```php
/**
 * @param   string   $name
 * @param   string   $surname
 * @return  bool
  */
```

**Printed by PHPStan PhpDocParser** :x:

```php
/**
 * @param string $name
 * @param string $surname
 * @return bool
 */
```

**Printed by Better PhpDocParser** :+1:

```php
/**
 * @param   string   $name
 * @param   string   $surname
 * @return  bool
 */
```

[Symplify\CodingStandard](https://github.com/symplify/codingstandard) and [Rector](https://github.com/rectorphp/rector) need to modify docblock and put it back in correct format. Other packages often put own spacing, or formats of specific tags.

**This package preserve original spacing**.

Thanks for [inspiration in *Format Preserving Printer* feature in `nikic/php-parser`](https://github.com/nikic/PHP-Parser/issues/487).

## Install

```bash
composer require symplify/better-phpdoc-parser
```

## Usage

Register services in your Symfony config:

```yaml
# services.yml
imports:
    - { resource: 'vendor/symplify/better-phpdoc-parser/config/config.yml' }
```

or register the needed services from `services.yml` in config of your other framework.

```php
<?php

use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;

class SomeClass
{
    public function __construct(PhpDocInfoFactory $phpDocInfoFactory, PhpDocInfoPrinter $phpDocInfoPrinter)
    {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
    }

    public function changeDocBlockAndPrintItBack(): string
    {
        $docComment = '/**    @var Type $variable    */';

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);

        // modify `$phpDocInfo` using its methods

        return $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo);
    }
}
```

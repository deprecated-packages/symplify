# Coding Standard

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/CodingStandard.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)

Set of PHP_CodeSniffer rules for Symplify packages.

## Install

```bash
composer require symplify/coding-standard --dev
```

## Usage

To use, check [EasyCodingStandard](/packages/EasyCodingStandard/README.md).


## Rules Overview

### FinalInterfaceSniff (Class)

- Non-abstract class that implements interface should be final.
- Except for Doctrine entities, they cannot be final.

```php
final class SomeClass implements SomeInterface
{
    public function run()
    {

    }
}
```


### BlockPropertyCommentSniff (Commenting)

- Block comment should be used instead of one liner

```php
class SomeClass
{
    /**
     * @var int
     */
    public $count;
}
```


### VarPropertyCommentSniff (Commenting)

- Property should have docblock comment.

```php
class SomeClass
{
    /**
     * @var int
     */
    private $someProperty;
}
```

### DebugFunctionCallSniff (Debug)

- Debug functions should not be left in the code


### ClassNamesWithoutPreSlashSniff (Namespaces)

- Class name after new/instanceof should not start with slash

```php
use Some\File;

$file = new File;
```


### AbstractClassNameSniff (Naming)

- Abstract class should have prefix "Abstract"


### InterfaceNameSniff (Naming)

- Interface should have suffix "Interface"



## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
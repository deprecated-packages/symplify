# CodeSniffer in PHP 7, with simple usage everyone understands

This is essential development tool that ensures your code **remains clean and consistent**.

[![Build Status](https://img.shields.io/travis/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://travis-ci.org/Symplify/PHP7_CodeSniffer)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/php7_codesniffer.svg?style=flat-square)](https://packagist.org/packages/symplify/php7_codesniffer)
[![Latest stable](https://img.shields.io/packagist/v/symplify/php7_codesniffer.svg?style=flat-square)](https://packagist.org/packages/symplify/php7_codesniffer)


## Install

```bash
composer require symplify/php7_codesniffer --dev
```

## Use

Run it from cli:

```bash
vendor/bin/php7cs src --standards=PSR2
```

To fix the issues just add `--fix`:

```bash
vendor/bin/php7cs src --standards=PSR2 --fix
```

### How to Use Specific Sniff Only?

```bash
vendor/bin/php7cs src --sniffs=PSR2.Classes.ClassDeclaration
vendor/bin/php7cs src --sniffs=PSR2.Classes.ClassDeclaration,Zend.Files.ClosingTag
```

You can combine them as well:

```bash
vendor/bin/php7cs src --standards=PSR2 --sniffs=Zend.Files.ClosingTag
```

### Or Use Standard WITHOUT One Sniff?

```bash
vendor/bin/php7cs src --standards=PSR2 --exclude-sniffs=PSR2.Namespaces.UseDeclaration
```

## Testing

```bash
bin/php7cs src tests --standards=PSR2
vendor/bin/phpunit
```


## Contributing

Rules are simple:

- new feature needs tests
- all tests must pass
- 1 feature per PR

I'd be happy to merge your feature then.

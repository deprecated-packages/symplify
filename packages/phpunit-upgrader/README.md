# PHPUnit Upgrades

[![Downloads total](https://img.shields.io/packagist/dt/symplify/phpunit-upgrader.svg?style=flat-square)](https://packagist.org/packages/symplify/phpunit-upgrader/stats)

Upgrade PHPUnit tests with smart helping hand.

## Install

```bash
composer require symplify/phpunit-upgrader --dev
```

## Usage

### 1. Change `assertContains()` on string calls to `assertStringContainsString()`

```bash
vendor/bin/phpunit-upgrader assert-contains /tests --error-report-file report.txt
```

Where `report.txt` is the output of PHPUnit run with fails:

```bash
1) Whatever::whatever
TypeError: Argument 2 passed to PHPUnit\Framework\Assert::assertContains() must be iterable, string given, called in somePath.php on line 100
```

### 2. Add `void` to `setUp()` and `tearDown()` methods

```bash
vendor/bin/phpunit-upgrader voids /tests
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).

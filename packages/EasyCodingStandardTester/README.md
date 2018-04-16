# The Best Way to Test Fixers and Sniffs

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandardTester/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandardTester)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard-tester.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard-tester)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Feasy-coding-standard-tester)

Do you write your own fixer and sniffs? Would you like to test them without having to learn a lot about their internals?

This package make testing them with 1 single class super easy.

## Install

```bash
composer require symplify/easy-coding-standard-tester --dev
```

## Usage

### A. Testing a Fixer from PHP CS Fixer

#### 1. Create a config with registered fixer:

```yaml
# /tests/Fixer/YourFixer/config.yml
services:
    Your\CondingStandard\Fixer\YourFixer: ~
```

#### 2. Create your test case extending `Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase` class

#### 3. Provide the config in `provideConfig()` method

#### 4. Make use of testing methods

- `doTestCorrectFile($correctFile)` - the file should not be affected by this checker
- `doTestWrongToFixedFile($wrongFile, $fixedFile)` - classic before/after testing

```php
<?php declare(strict_types=1);

namespace Your\CodingStandard\Tests\Fixer\YourFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class YourFixerTest extends AbstractCheckerTestCase
{
    public function testCorrectCases(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');
    }

    public function testWrongToFixedCases(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc');
    }

    // method required by abstract class, to provide path to the config with 1 or more fixers/sniffs
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
```

### B. Testing a Sniff from PHP_CodeSniffer

I got surprise for you - everything is the same.

```yaml
# /tests/Fixer/YourFixer/config.yml
services:
    Your\CondingStandard\Sniff\YourSniff: ~
```

There is just one extra method:

- `doTestWrongFile($wrongFile)` - when sniff doesn't fix the error, but only finds it

```php
<?php declare(strict_types=1);

namespace Your\CodingStandard\Tests\Sniff\YourSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class YourSniffTest extends AbstractCheckerTestCase
{
    // this is the same
    public function testCorrectCases(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');
    }

    // this is the same, in case of fixing sniff
    public function testWrongToFixedCases(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc');
    }

    // this one is extra for sniff, that only detects errors
    public function testWrongCases(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
```

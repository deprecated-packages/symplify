# The Best Way to Test Fixers and Sniffs

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandardTester/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandardTester)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard-tester.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard-tester/stats)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Feasy-coding-standard-tester)

Do you write your own fixer and sniffs? Would you like to test them without having to learn a lot about their internals?

**This package make fixer and sniff testing with 1 single approach super easy**.

## Install

```bash
composer require symplify/easy-coding-standard-tester --dev
```

## Usage

1. Create a config with checker(s) you want to test

```yaml
# /tests/Fixer/YourFixer/config.yml
services:
    Your\CondingStandard\Fixer\YourFixer: ~

    # or
    Your\CondingStandard\Sniff\YourSniff: ~

    # or even more, if you want to test whole sets (like PSR-12)
```

2. Create a test case extending `Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase` class

3. Provide path to the config above in `provideConfig()` method

4. Make use of testing methods in your test case

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

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
```

### Non-Fixing Sniff?

There is one extra method for sniff that doesn't fix the error, but only finds it:

- `doTestWrongFile($wrongFile)`

```php
<?php declare(strict_types=1);

namespace Your\CodingStandard\Tests\Sniff\YourSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class YourSniffTest extends AbstractCheckerTestCase
{
    // ...

    public function testWrongCases(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.php.inc');
    }
}
```

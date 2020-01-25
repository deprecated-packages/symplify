# The Best Way to Test Fixers and Sniffs

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard-tester.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard-tester/stats)

Do you write your own fixer and sniffs? Would you like to test them without having to learn a lot about their internals?

**This package make fixer and sniff testing with 1 single approach super easy**.

## Install

```bash
composer require symplify/easy-coding-standard-tester --dev
```

## Usage

1. Extend `Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase` class

2. Provide files to `doTestFiles()` method

```php
<?php

declare(strict_types=1);

namespace Your\CodingStandard\Tests\Fixer\YourFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class YourFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/correct/correct.php.inc', // matches "correct" → 0 errors
            __DIR__ . '/wrong/wrong.php.inc', // matches "wrong" → at least 1 error
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'] // 2 items in array → wrong to fixed
        ]);
    }

    protected function getCheckerClass(): string
    {
        return \Your\CondingStandard\Fixer\YourFixer::class;
    }
}
```

Instead of `[__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc']` you can use single file: `__DIR__ . '/fixture/fixture.php.inc'` in this format:

```php
<?php

$array = array();

?>
-----
<?php

$array = [];

?>
```

```bash
before
------
after
```

### Non-Fixing Sniff?

There is one extra method for sniff that doesn't fix the error, but only finds it:

- `doTestWrongFile($wrongFile)`

```php
<?php

declare(strict_types=1);

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

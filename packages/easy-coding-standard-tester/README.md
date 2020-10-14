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

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Your\CondingStandard\Fixer\YourFixer;

final class YourFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideDataWithFileErrors()
     */
    public function testFileErrors(SmartFileInfo $fileInfo, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($fileInfo, $expectedErrorCount);
    }

    public function provideDataWithFileErrors(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/wrong.php.inc'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/correct.php.inc'), 0];
    }

    protected function getCheckerClass(): string
    {
        return YourFixer::class;
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

In pseudo-code:

```bash
before
------
after
```

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).

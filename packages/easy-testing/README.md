# Testing Made Easy

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-testing.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-testing/stats)

## Install

```bash
composer require symplify/easy-testing --dev
```

## Usage

### Easier working with Fixtures

Do you use unit fixture file format?

```php
<?php

echo 'content before';

?>
-----
<?php

echo 'content after';

?>
```

Or in case of no change at all:

```php
<?php

echo 'just this content';
```

The code is separated by `-----`. Top half of the file is input, the 2nd half is excpeted output.

It is common to organize test fixture in the test directory:

```bash
/tests/SomeTest/Fixture/added_comma.php.inc
/tests/SomeTest/Fixture/skip_alreay_added_comma.php.inc
```

<br>

How this package makes it easy to work with them? 2 classes:

- `Symplify\EasyTesting\DataProvider\StaticFixtureFinder`
- `Symplify\EasyTesting\Fixture\FixtureSplitter`

```php
// tests/SomeTest/SomeTest.php

namespace App\Tests\SomeTest;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\Fixture\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SomeTest extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo)
    {
        [$inputContent, $expectedContent] = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fileInfo);

        // test before content
        $someService = new SomeService();
        $changedContent = $someService->process($inputContent);

        $this->assertSame($expectedContent, $changedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
    }
}
```

## Features

Do you need the input code to be in separated files? E.g. to test the file was moved?

Instead of `splitFileInfoToInputAndExpected()` use `splitFileInfoToLocalInputAndExpectedFileInfos()`:

```diff
 $fixtureSplitter = new FixtureSplitter();
-[$inputContent, $expectedContent] = $fixtureSplitter->splitFileInfoToInputAndExpected($fileInfo);
+[$inputFileInfo, $expectedFileInfo] = $fixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos($fileInfo);
```

Compared to formated method, `splitFileInfoToLocalInputAndExpectedFileInfos()` will:

- separate fixture to input and expected content
- save them both as separated files to temporary path
- optionally autoload the first one, e.g. if you need it for Reflection

```php
[$inputFileInfo, $expectedFileInfo] = $fixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos($fileInfo, true);
```

<br>

By default, the `StaticFixtureFinder` finds only `*.php.inc` files.

```php
return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
```

In case you use different files, e.g. `*.twig` or `*.md`, change it in 2nd argument:

```php
return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.md');
```

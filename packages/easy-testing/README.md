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

echo 'content before';

?>
-----
<?php

echo 'content after';

?>
```

Or in case of no change at all:

```php
<?php echo 'just this content';
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
- `Symplify\EasyTesting\StaticFixtureSplitter`

```php
<?php // tests/SomeTest/SomeTest.php

namespace App\Tests\SomeTest;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SomeTest extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fileInfo);

        // test before content
        $someService = new SomeService();
        $changedContent = $someService->process($inputAndExpected->getInput());

        $this->assertSame($inputAndExpected->getExpected(), $changedContent);
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
-$inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected(
+$inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
    $fileInfo
 );
```

Compared to formated method, `splitFileInfoToLocalInputAndExpectedFileInfos()` will:

- separate fixture to input and expected content
- save them both as separated files to temporary path
- optionally autoload the first one, e.g. if you need it for Reflection

```php
<?php use Symplify\EasyTesting\StaticFixtureSplitter;

$inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
    $fileInfo,
    true
);
```

<br>

By default, the `StaticFixtureFinder` finds only `*.php.inc` files.

```php
<?php use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;

return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
```

In case you use different files, e.g. `*.twig` or `*.md`, change it in 2nd argument:

```php
<?php use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;

return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.md');
```

## Updating Fixture

[How to Update Hundreds of Test Fixtures with Single PHPUnit run](https://tomasvotruba.com/blog/2020/07/20/how-to-update-hundreds-of-test-fixtures-with-single-phpunit-run/)?

If you change an output of your software on purpose, you might want to update your fixture. Manually? No, from command line:

```bash
UPDATE_TESTS=1 vendor/bin/phpunit
UT=1 vendor/bin/phpunit
```

To make this work, we have to add `StaticFixtureUpdater::updateFixtureContent()` call to our test case:

```php
<?php use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SomeTestCase extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputFileInfoAndExpectedFileInfo = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        // process content
        $currentContent = '...';

        // here we update test fixture if the content changed
        StaticFixtureUpdater::updateFixtureContent(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo(),
            $currentContent,
            $fixtureFileInfo
        );
    }

    // data provider...
}
```

## Assert 2 Directories by Files and Content

Do you generate large portion of files? Do you want to skip nitpicking tests file by file?

Use `assertDirectoryEquals()` method to validate the files and their content is as expected.

```php
<?php use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\PHPUnit\Behavior\DirectoryAssertableTrait;

final class DirectoryAssertableTraitTest extends TestCase
{
    use DirectoryAssertableTrait;

    public function testSuccess(): void
    {
        $this->assertDirectoryEquals(__DIR__ . '/Fixture/first_directory', __DIR__ . '/Fixture/second_directory');
    }
}
```

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).

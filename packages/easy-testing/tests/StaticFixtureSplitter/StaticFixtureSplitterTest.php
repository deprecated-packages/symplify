<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Tests\StaticFixtureSplitter;

use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StaticFixtureSplitterTest extends TestCase
{
    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Source/simple_fixture.php.inc');

        [$inputContent, $expectedContent] = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fileInfo);

        $this->assertSame('a' . PHP_EOL, $inputContent);
        $this->assertSame('b' . PHP_EOL, $expectedContent);
    }
}

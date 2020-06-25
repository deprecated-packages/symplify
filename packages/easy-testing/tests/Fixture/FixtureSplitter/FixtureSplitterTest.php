<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Tests\Fixture\FixtureSplitter;

use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\Fixture\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixtureSplitterTest extends TestCase
{
    public function test()
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Source/simple_fixture.php.inc');

        [$inputContent, $expectedContent] = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fileInfo);

        $this->assertSame('a' . PHP_EOL, $inputContent);
        $this->assertSame('b' . PHP_EOL, $expectedContent);
    }
}

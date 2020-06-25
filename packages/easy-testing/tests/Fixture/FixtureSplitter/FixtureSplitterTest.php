<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Tests\Fixture\FixtureSplitter;

use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\Fixture\FixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixtureSplitterTest extends TestCase
{
    /**
     * @var FixtureSplitter
     */
    private $fixtureSplitter;

    protected function setUp(): void
    {
        $this->fixtureSplitter = new FixtureSplitter();
    }

    public function test()
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Source/simple_fixture.php.inc');

        [$inputContent, $expectedContent] = $this->fixtureSplitter->splitFileInfoToInputAndExpected($fileInfo);

        $this->assertSame('a' . PHP_EOL, $inputContent);
        $this->assertSame('b' . PHP_EOL, $expectedContent);
    }
}

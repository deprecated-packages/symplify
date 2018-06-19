<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogLinker;

use Iterator;
use Symplify\ChangelogLinker\Tests\AbstractWorkerTestCase;

/**
 * @covers \Symplify\ChangelogLinker\ChangelogLinker
 */
final class ChangelogLinkerTest extends AbstractWorkerTestCase
{
    /**
     * @dataProvider dataProvider()
     */
    public function test(string $originalFile, string $expectedFile): void
    {
        $processedFile = $this->doProcess($originalFile);

        $this->assertStringEqualsFile($expectedFile, $processedFile);
    }

    public function dataProvider(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
        yield [__DIR__ . '/Source/before/02.md', __DIR__ . '/Source/after/02.md'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/Source/config.yml';
    }
}

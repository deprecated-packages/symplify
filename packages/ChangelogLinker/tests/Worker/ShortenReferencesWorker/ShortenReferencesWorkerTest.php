<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\ShortenReferencesWorker;

use Iterator;
use Symplify\ChangelogLinker\Tests\AbstractWorkerTestCase;

/**
 * @covers \Symplify\ChangelogLinker\Worker\ShortenReferencesWorker
 */
final class ShortenReferencesWorkerTest extends AbstractWorkerTestCase
{
    /**
     * @dataProvider dataProvider()
     */
    public function test(string $originalFile, string $expectedFile): void
    {
        $this->assertStringEqualsFile($expectedFile, $this->doProcess($originalFile));
    }

    public function dataProvider(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/Source/config.yml';
    }
}

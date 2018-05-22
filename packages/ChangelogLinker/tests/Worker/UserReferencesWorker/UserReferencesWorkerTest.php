<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\UserReferencesWorker;

use Iterator;
use Symplify\ChangelogLinker\Tests\AbstractWorkerTestCase;
use Symplify\ChangelogLinker\Worker\UserReferencesWorker;

final class UserReferencesWorkerTest extends AbstractWorkerTestCase
{
    /**
     * @dataProvider dataProvider()
     */
    public function test(string $originalFile, string $expectedFile): void
    {
        $this->assertStringEqualsFile($expectedFile, $this->doProcess($originalFile, UserReferencesWorker::class));
    }

    public function dataProvider(): Iterator
    {
//        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
//        yield [__DIR__ . '/Source/before/02.md', __DIR__ . '/Source/after/02.md'];
        yield [__DIR__ . '/Source/before/03.md', __DIR__ . '/Source/after/03.md'];
    }
}

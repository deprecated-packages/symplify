<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\DiffLinksToVersionsWorker;

use Iterator;
use Symplify\ChangelogLinker\Tests\AbstractWorkerTestCase;
use Symplify\ChangelogLinker\Worker\DiffLinksToVersionsWorker;

final class DiffLinksToVersionsWorkerTest extends AbstractWorkerTestCase
{
    /**
     * @dataProvider dataProvider()
     */
    public function test(string $originalFile, string $expectedFile): void
    {
        $this->doProcess($originalFile, $expectedFile, DiffLinksToVersionsWorker::class);
    }

    public function dataProvider(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
    }
}

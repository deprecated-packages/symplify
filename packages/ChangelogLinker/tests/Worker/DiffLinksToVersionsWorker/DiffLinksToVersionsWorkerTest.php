<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\DiffLinksToVersionsWorker;

use Iterator;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Tests\AbstractContainerAwareTestCase;
use Symplify\ChangelogLinker\Worker\DiffLinksToVersionsWorker;

final class DiffLinksToVersionsWorkerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $this->changelogApplication = $this->container->get(ChangelogApplication::class);
    }

    /**
     * @dataProvider provideInputAndExpectedOutputFiles()
     */
    public function testProcess(string $originalFile, string $expectedFile): void
    {
        $processedFile = $this->changelogApplication->processFileWithSingleWorker(
            $originalFile,
            DiffLinksToVersionsWorker::class
        );
        $this->assertStringEqualsFile($expectedFile, $processedFile);
    }

    public function provideInputAndExpectedOutputFiles(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
    }
}

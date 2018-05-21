<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\ReleaseReferencesWorker;

use Iterator;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Tests\AbstractContainerAwareTestCase;
use Symplify\ChangelogLinker\Worker\ReleaseReferencesWorker;

final class ReleaseReferencesWorkerTest extends AbstractContainerAwareTestCase
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
            ReleaseReferencesWorker::class
        );
        $this->assertStringMatchesFormatFile($expectedFile, $processedFile);
    }

    public function provideInputAndExpectedOutputFiles(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
        yield [__DIR__ . '/Source/before/02.md', __DIR__ . '/Source/after/02.md'];
    }
}

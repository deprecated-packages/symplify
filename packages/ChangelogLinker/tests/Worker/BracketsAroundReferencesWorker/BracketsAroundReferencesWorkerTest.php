<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\BracketsAroundReferencesWorker;

use Iterator;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Tests\AbstractContainerAwareTestCase;
use Symplify\ChangelogLinker\Worker\BracketsAroundReferencesWorker;

final class BracketsAroundReferencesWorkerTest extends AbstractContainerAwareTestCase
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
     * @dataProvider dataProvider
     */
    public function testProcess(string $originalFile, string $expectedFile): void
    {
        $processedFile = $this->changelogApplication->processFileWithSingleWorker(
            $originalFile,
            BracketsAroundReferencesWorker::class
        );

        $this->assertStringEqualsFile($expectedFile, $processedFile);
    }

    public function dataProvider(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
        yield [__DIR__ . '/Source/before/02.md', __DIR__ . '/Source/after/02.md'];
        yield [__DIR__ . '/Source/before/03.md', __DIR__ . '/Source/after/03.md'];
        yield [__DIR__ . '/Source/before/04.md', __DIR__ . '/Source/after/04.md'];
    }
}

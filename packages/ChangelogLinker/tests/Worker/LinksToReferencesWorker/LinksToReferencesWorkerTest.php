<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\LinksToReferencesWorker;

use Iterator;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Tests\AbstractContainerAwareTestCase;

final class LinksToReferencesWorkerTest extends AbstractContainerAwareTestCase
{
    private $processedFile;
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
        $processedFile = $this->changelogApplication->processFile($originalFile);
        $this->assertStringEqualsFile($expectedFile, $processedFile);
    }

    public function dataProvider(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
        yield [__DIR__ . '/Source/before/02.md', __DIR__ . '/Source/after/02.md'];
    }
}

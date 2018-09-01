<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogCleaner;

use Iterator;
use Nette\Utils\FileSystem;
use Symplify\ChangelogLinker\ChangelogCleaner;
use Symplify\ChangelogLinker\Tests\AbstractContainerAwareTestCase;

final class ChangelogCleanerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ChangelogCleaner
     */
    private $changelogCleaner;

    protected function setUp(): void
    {
        $this->changelogCleaner = $this->container->get(ChangelogCleaner::class);
    }

    /**
     * @dataProvider dataProvider()
     */
    public function test(string $originalFile, string $expectedFile): void
    {
        $processedFile = $this->changelogCleaner->processContent(FileSystem::read($originalFile));

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

<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\LinkifyWorker;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\DependencyInjection\ContainerFactory;
use Symplify\ChangelogLinker\Worker\LinkifyWorker;

final class LinkifyWorkerTest extends TestCase
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/Source/config.yml');

        $this->changelogApplication = $container->get(ChangelogApplication::class);
    }

    /**
     * @dataProvider dataProvider()
     */
    public function test(string $originalFile, string $expectedFile): void
    {
        $processedFile = $this->changelogApplication->processFileWithSingleWorker($originalFile, LinkifyWorker::class);

        $this->assertStringEqualsFile($expectedFile, $processedFile);
    }

    public function dataProvider(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
    }
}

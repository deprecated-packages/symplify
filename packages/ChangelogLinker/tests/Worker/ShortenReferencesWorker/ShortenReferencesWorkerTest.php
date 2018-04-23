<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\ShortenReferencesWorker;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Worker\ShortenReferencesWorker;

final class ShortenReferencesWorkerTest extends TestCase
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $this->changelogApplication = new ChangelogApplication('https://github.com/Symplify/Symplify');
        $this->changelogApplication->addWorker(new ShortenReferencesWorker());
    }

    /**
     * @dataProvider provideInputAndExpectedOutputFiles()
     */
    public function testProcess(string $originalFile, string $processedFile): void
    {
        $this->assertStringEqualsFile($processedFile, $this->changelogApplication->processFile($originalFile));
    }

    public function provideInputAndExpectedOutputFiles(): Iterator
    {
        yield [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'];
    }
}

<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\BracketsAroundReferencesWorker;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Worker\BracketsAroundReferencesWorker;

final class BracketsAroundReferencesWorkerTest extends TestCase
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $this->changelogApplication = new ChangelogApplication('https://github.com/Symplify/Symplify');
        $this->changelogApplication->addWorker(new BracketsAroundReferencesWorker());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testProcess(string $originalFile, string $processedFile): void
    {
        $this->assertStringEqualsFile($processedFile, $this->changelogApplication->processFile($originalFile));
    }

    /**
     * @return mixed[][]
     */
    public function dataProvider(): array
    {
        return [
            [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'],
            [__DIR__ . '/Source/before/02.md', __DIR__ . '/Source/after/02.md'],
            [__DIR__ . '/Source/before/03.md', __DIR__ . '/Source/after/03.md'],
        ];
    }
}

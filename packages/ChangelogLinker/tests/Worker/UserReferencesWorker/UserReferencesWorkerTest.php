<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Worker\UserReferencesWorker;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogApplication;
use Symplify\ChangelogLinker\Worker\UserReferencesWorker;

final class UserReferencesWorkerTest extends TestCase
{
    /**
     * @var ChangelogApplication
     */
    private $changelogApplication;

    protected function setUp(): void
    {
        $this->changelogApplication = new ChangelogApplication('https://github.com/Symplify/Symplify');
        $this->changelogApplication->addWorker(new UserReferencesWorker());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testProcess(string $originalFile, string $expectedFile): void
    {
        $this->assertStringEqualsFile($expectedFile, $this->changelogApplication->processFile($originalFile));
    }

    /**
     * @return mixed[][]
     */
    public function dataProvider(): array
    {
        return [
            [__DIR__ . '/Source/before/01.md', __DIR__ . '/Source/after/01.md'],
            [__DIR__ . '/Source/before/02.md', __DIR__ . '/Source/after/02.md'],
        ];
    }
}

<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\IdsAnalyzer;

use Iterator;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;

final class IdsAnalyzerTest extends TestCase
{
    /**
     * @var IdsAnalyzer
     */
    private $idsAnalyzer;

    protected function setUp(): void
    {
        $this->idsAnalyzer = new IdsAnalyzer();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath, int $expectedId): void
    {
        $content = FileSystem::read($filePath);
        $this->assertSame($expectedId, $this->idsAnalyzer->getHighestIdInChangelog($content));
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Source/SomeFile.md', 15];
        yield [__DIR__ . '/Source/SomeFileWithLinks.md', 20];
        yield [__DIR__ . '/Source/SomeFileWithAnotherType.md', 428];
        yield [__DIR__ . '/Source/ShopsysChangelog.md', 449];
    }
}

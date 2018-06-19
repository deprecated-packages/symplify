<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\IdsAnalyzer;

use Iterator;
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
        $content = file_get_contents($filePath);
        $this->assertSame($expectedId, $this->idsAnalyzer->getHighestIdInChangelog($content));
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Source/SomeFile.md', 15];
        yield [__DIR__ . '/Source/SomeFileWithLinks.md', 20];
    }
}

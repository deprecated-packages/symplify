<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\IdsAnalyzer;

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

    public function test(): void
    {
        $content = file_get_contents(__DIR__ . '/Source/SomeFile.md');
        $this->assertSame(15, $this->idsAnalyzer->getHighestIdInChangelog($content));

        $content = file_get_contents(__DIR__ . '/Source/SomeFileWithLinks.md');
        $this->assertSame(20, $this->idsAnalyzer->getHighestIdInChangelog($content));
    }
}

<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\LinksAnalyzer;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;

final class LinksAnalyzerTest extends TestCase
{
    /**
     * @var LinksAnalyzer
     */
    private $linksAnalyzer;

    protected function setUp(): void
    {
        $this->linksAnalyzer = new LinksAnalyzer();
    }

    public function test(): void
    {
        $this->linksAnalyzer->analyzeContent(FileSystem::read(__DIR__ . '/Source/SomeFile.md'));

        $this->assertTrue($this->linksAnalyzer->hasLinkedId('5'));
        $this->assertFalse($this->linksAnalyzer->hasLinkedId('10'));
    }

    public function testDeadLinks(): void
    {
        $this->linksAnalyzer->analyzeContent(FileSystem::read(__DIR__ . '/Source/SomeFileWithDeadlinks.md'));

        $deadLinks = $this->linksAnalyzer->getDeadLinks();

        $this->assertSame(['5', '15'], $deadLinks);
    }
}

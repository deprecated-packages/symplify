<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\LinksAnalyzer;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LinksAnalyzerTest extends TestCase
{
    /**
     * @var LinksAnalyzer
     */
    private $linksAnalyzer;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->linksAnalyzer = new LinksAnalyzer();
        $this->smartFileSystem = new SmartFileSystem();
    }

    public function test(): void
    {
        $fileContent = $this->smartFileSystem->readFile(__DIR__ . '/Source/SomeFile.md');
        $this->linksAnalyzer->analyzeContent($fileContent);

        $this->assertTrue($this->linksAnalyzer->hasLinkedId('5'));
        $this->assertFalse($this->linksAnalyzer->hasLinkedId('10'));
    }

    public function testDeadLinks(): void
    {
        $fileContent = $this->smartFileSystem->readFile(__DIR__ . '/Source/SomeFileWithDeadlinks.md');
        $this->linksAnalyzer->analyzeContent($fileContent);

        $deadLinks = $this->linksAnalyzer->getDeadLinks();

        $this->assertSame(['5', '15'], $deadLinks);
    }
}

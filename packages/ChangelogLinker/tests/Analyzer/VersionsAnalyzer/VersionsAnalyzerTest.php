<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\VersionsAnalyzer;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;

final class VersionsAnalyzerTest extends TestCase
{
    /**
     * @var VersionsAnalyzer
     */
    private $versionsAnalyzer;

    protected function setUp(): void
    {
        $this->versionsAnalyzer = new VersionsAnalyzer();
    }

    public function test(): void
    {
        $this->versionsAnalyzer->analyzeContent(file_get_contents(__DIR__ . '/Source/SomeFile.md'));

        $this->assertCount(2, $this->versionsAnalyzer->getVersions());

        $this->assertTrue($this->versionsAnalyzer->hasLinkedVersion('v4.0.0'));
        $this->assertFalse($this->versionsAnalyzer->hasLinkedVersion('v10.0.0'));

        // has to be wrapped in []
        $this->assertFalse($this->versionsAnalyzer->hasLinkedVersion('v3.0.0'));

        $this->assertTrue($this->versionsAnalyzer->isLastVersion('v4.0.0'));
        $this->assertFalse($this->versionsAnalyzer->isLastVersion('v3.5.0'));
    }
}

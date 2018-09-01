<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\VersionsAnalyzer;

use Iterator;
use Nette\Utils\FileSystem;
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
        $this->versionsAnalyzer->analyzeContent(FileSystem::read(__DIR__ . '/Source/SomeFile.md'));
    }

    /**
     * @dataProvider provideDataHasLinkedVersion()
     */
    public function testHasLinkedVersion(string $version, bool $hasLinkedVersion): void
    {
        $this->assertSame($hasLinkedVersion, $this->versionsAnalyzer->hasLinkedVersion($version));
    }

    public function provideDataHasLinkedVersion(): Iterator
    {
        yield ['v4.0.0', true];
        yield ['v10.0.0', false];

        // has to be wrapped in []
        yield ['v3.0.0', false];
    }

    /**
     * @dataProvider provideDataIsLastVersion()
     */
    public function testIsLastVersion(string $version, bool $hasLinkedVersion): void
    {
        $this->assertSame($hasLinkedVersion, $this->versionsAnalyzer->isLastVersion($version));
    }

    public function provideDataIsLastVersion(): Iterator
    {
        yield ['v4.0.0', true];
        yield ['v3.5.0', false];
    }
}

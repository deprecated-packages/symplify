<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\VersionsAnalyzer;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;
use Symplify\SmartFileSystem\SmartFileSystem;

final class VersionsAnalyzerTest extends TestCase
{
    public function test(): void
    {
        $versionsAnalyzer = new VersionsAnalyzer();
        $smartFileSystem = new SmartFileSystem();

        $versionsAnalyzer->analyzeContent($smartFileSystem->readFile(__DIR__ . '/Source/SomeFile.md'));

        $this->assertCount(3, $versionsAnalyzer->getVersions());
        $this->assertSame(['v4.0.0', 'v3.5.0', 'v3.0.0'], $versionsAnalyzer->getVersions());
    }
}

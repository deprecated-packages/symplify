<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\VersionsAnalyzer;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\VersionsAnalyzer;

final class VersionsAnalyzerTest extends TestCase
{
    public function test(): void
    {
        $versionsAnalyzer = new VersionsAnalyzer();
        $versionsAnalyzer->analyzeContent(FileSystem::read(__DIR__ . '/Source/SomeFile.md'));

        $this->assertCount(3, $versionsAnalyzer->getVersions());
        $this->assertSame(['v4.0.0', 'v3.5.0', 'v3.0.0'], $versionsAnalyzer->getVersions());
    }
}

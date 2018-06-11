<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\IdsAnalyzer;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;

final class IdsAnalyzerTest extends TestCase
{
    /**
     * @var IdsAnalyzer
     */
    private $linksAnalyzer;

    protected function setUp(): void
    {
        $this->linksAnalyzer = new IdsAnalyzer();
    }

    public function test(): void
    {
        $this->assertSame(10,
            $this->linksAnalyzer->getLastIdInChangelog(__DIR__ . '/Source/SomeFile.md')
        );
    }
}

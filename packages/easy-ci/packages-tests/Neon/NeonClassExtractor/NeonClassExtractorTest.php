<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Neon\NeonClassExtractor;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\Neon\NeonClassExtractor;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonClassExtractorTest extends TestCase
{
    private NeonClassExtractor $neonClassExtractor;

    protected function setUp(): void
    {
        $this->neonClassExtractor = new NeonClassExtractor();
    }

    /**
     * @dataProvider provideData()
     * @param string[] $expectedClasses
     */
    public function test(SmartFileInfo $smartFileInfo, array $expectedClasses): void
    {
        $extractedClasses = $this->neonClassExtractor->extract($smartFileInfo);
        $this->assertSame($expectedClasses, $extractedClasses);
    }

    /**
     * @return Iterator<array<int, string[]>|SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/some_services.neon'), ['App\FirstService']];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/phpstan_rules.neon'), ['App\FirstRule']];
    }
}

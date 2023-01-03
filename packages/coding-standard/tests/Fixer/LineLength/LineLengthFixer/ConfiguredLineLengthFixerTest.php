<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;

final class ConfiguredLineLengthFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public function provideData(): Iterator
    {
        yield self::yieldFiles(__DIR__ . '/FixtureConfigured');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/custom_rule.php';
    }
}

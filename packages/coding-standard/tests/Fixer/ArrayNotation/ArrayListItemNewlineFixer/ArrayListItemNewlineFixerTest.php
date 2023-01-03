<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayListItemNewlineFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;

final class ArrayListItemNewlineFixerTest extends AbstractCheckerTestCase
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
        yield self::yieldFiles(__DIR__ . '/Fixture');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}

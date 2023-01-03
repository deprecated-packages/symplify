<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;

final class Issue973Test extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/correct973.php.inc'];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/line_lenght_rule.php';
    }
}

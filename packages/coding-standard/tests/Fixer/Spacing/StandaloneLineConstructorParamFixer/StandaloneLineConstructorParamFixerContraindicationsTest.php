<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineConstructorParamFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;

final class StandaloneLineConstructorParamFixerContraindicationsTest extends AbstractCheckerTestCase
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
        yield self::yieldFiles(__DIR__ . '/FixtureContraindications');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/contraindications.php';
    }
}

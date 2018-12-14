<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue972Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong972.php.inc']);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config972.yml';
    }
}

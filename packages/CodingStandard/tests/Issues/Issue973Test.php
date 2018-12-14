<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue973Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/correct973.php.inc']);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config973.yml';
    }
}

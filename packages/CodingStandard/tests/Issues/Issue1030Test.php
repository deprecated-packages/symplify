<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue1030Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct1030.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config1030.yml';
    }
}

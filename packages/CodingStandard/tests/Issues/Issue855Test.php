<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue855Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct855.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config855.yml';
    }
}

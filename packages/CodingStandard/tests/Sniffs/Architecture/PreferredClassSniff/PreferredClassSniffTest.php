<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\PreferredClassSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff
 */
final class PreferredClassSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong3.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}

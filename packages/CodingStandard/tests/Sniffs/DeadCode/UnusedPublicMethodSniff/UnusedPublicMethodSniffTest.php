<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DeadCode\UnusedPublicMethodSniff;

use Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff
 */
final class UnusedPublicMethodSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
        ], function (): void {
            // to reset the cache inside the Sniff
            $unusedPublicMethodSniff = $this->getContainer()->get(UnusedPublicMethodSniff::class);
            $unusedPublicMethodSniff->reset();
        });
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}

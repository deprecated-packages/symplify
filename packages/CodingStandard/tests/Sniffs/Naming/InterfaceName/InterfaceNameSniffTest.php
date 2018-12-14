<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\InterfaceName;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff
 */
final class InterfaceNameSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/correct2.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}

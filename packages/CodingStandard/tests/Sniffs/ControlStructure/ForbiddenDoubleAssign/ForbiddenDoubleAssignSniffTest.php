<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructure\ForbiddenDoubleAssign;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\ControlStructure\ForbiddenDoubleAssignSniff
 */
final class ForbiddenDoubleAssignSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/correct2.php.inc',
            __DIR__ . '/Fixture/wrong.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}

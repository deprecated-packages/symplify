<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\ClassNameSuffixByParent;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff
 */
final class ListConfiguredTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong5.php.inc',
            __DIR__ . '/Fixture/wrong6.php.inc',
            __DIR__ . '/Fixture/wrong7.php.inc',
            __DIR__ . '/Fixture/correct5.php.inc',
            __DIR__ . '/Fixture/correct6.php.inc',
            __DIR__ . '/Fixture/correct7.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/list-configured-config.yml';
    }
}

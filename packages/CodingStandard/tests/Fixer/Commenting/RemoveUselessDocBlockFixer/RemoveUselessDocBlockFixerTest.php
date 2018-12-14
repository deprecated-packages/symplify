<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDocBlockFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer
 */
final class RemoveUselessDocBlockFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/correct.php.inc',
            __DIR__ . '/Fixture/correct2.php.inc',
            __DIR__ . '/Fixture/correct3.php.inc',
            __DIR__ . '/Fixture/correct4.php.inc',
            __DIR__ . '/Fixture/correct5.php.inc',
            __DIR__ . '/Fixture/correct6.php.inc',
            __DIR__ . '/Fixture/correct7.php.inc',
            __DIR__ . '/Fixture/correct8.php.inc',
            __DIR__ . '/Fixture/correct9.php.inc',
            __DIR__ . '/Fixture/correct10.php.inc',
            __DIR__ . '/Fixture/correct11.php.inc',
            __DIR__ . '/Fixture/correct12.php.inc',
            __DIR__ . '/Fixture/correct13.php.inc',
            __DIR__ . '/Fixture/correct14.php.inc',
            __DIR__ . '/Fixture/correct15.php.inc',
            __DIR__ . '/Fixture/correct16.php.inc',
            __DIR__ . '/Fixture/correct17.php.inc',
            __DIR__ . '/Fixture/correct18.php.inc',
        ]);

        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong3.php.inc',
            __DIR__ . '/Fixture/wrong4.php.inc',
            __DIR__ . '/Fixture/wrong5.php.inc',
            __DIR__ . '/Fixture/wrong6.php.inc',
            __DIR__ . '/Fixture/wrong7.php.inc',
            __DIR__ . '/Fixture/wrong8.php.inc',
            __DIR__ . '/Fixture/wrong9.php.inc',
            __DIR__ . '/Fixture/wrong10.php.inc',
            __DIR__ . '/Fixture/wrong11.php.inc',
            __DIR__ . '/Fixture/wrong12.php.inc',
            __DIR__ . '/Fixture/wrong15.php.inc',
            __DIR__ . '/Fixture/wrong16.php.inc',
            __DIR__ . '/Fixture/wrong17.php.inc',
            __DIR__ . '/Fixture/wrong18.php.inc',
            __DIR__ . '/Fixture/wrong19.php.inc',
            __DIR__ . '/Fixture/wrong20.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}

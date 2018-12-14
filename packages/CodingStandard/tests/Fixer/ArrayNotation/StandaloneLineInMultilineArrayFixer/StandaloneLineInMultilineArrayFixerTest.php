<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer
 */
final class StandaloneLineInMultilineArrayFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/fixture/correct.php.inc',
            __DIR__ . '/fixture/correct2.php.inc',
            __DIR__ . '/fixture/correct3.php.inc',
            __DIR__ . '/fixture/correct4.php.inc',
            __DIR__ . '/fixture/correct5.php.inc',
            __DIR__ . '/fixture/correct6.php.inc',
            __DIR__ . '/fixture/wrong.php.inc',
            __DIR__ . '/fixture/wrong2.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}

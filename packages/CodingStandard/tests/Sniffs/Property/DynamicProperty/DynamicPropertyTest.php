<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Property\DynamicProperty;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\Property\DynamicPropertySniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class DynamicPropertyTest extends AbstractSniffTestCase
{
    /**
     * @dataProvider provideWrongCases()
     */
    public function testWrong(string $file): void
    {
        $this->doTestWrongFile($file);
    }

    /**
     * @return string[][]
     */
    public function provideWrongCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc'],
        ];
    }

    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrect(string $file): void
    {
        $this->doTestCorrectFile($file);
    }

    /**
     * @return string[][]
     */
    public function provideCorrectCases(): array
    {
        return [
            [__DIR__ . '/correct/correct.php.inc'],
            [__DIR__ . '/correct/correct2.php.inc'],
            [__DIR__ . '/correct/correct3.php.inc'],
            [__DIR__ . '/correct/correct4.php.inc'],
        ];
    }

    protected function createSniff(): Sniff
    {
        return new DynamicPropertySniff();
    }
}

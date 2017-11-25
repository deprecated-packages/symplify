<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DependencyInjection\NoClassInstantiation;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\DependencyInjection\NoClassInstantiationSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class NoClassInstantiationSniffTest extends AbstractSniffTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.php.inc');
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
            [__DIR__ . '/correct/correct5.php.inc'],
            [__DIR__ . '/correct/correct6.php.inc'],
            [__DIR__ . '/correct/correct7.php.inc'],
            [__DIR__ . '/correct/correct8.php.inc'],
        ];
    }

    protected function createSniff(): Sniff
    {
        return new NoClassInstantiationSniff();
    }
}

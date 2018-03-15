<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Commenting\ForbiddenReferenceSniff;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenReferenceSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class ForbiddenReferenceSniffTest extends AbstractSniffTestCase
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
    public function provideWrongCases(): iterable
    {
        yield [__DIR__ . '/wrong/wrong.php.inc'];
    }

    protected function createSniff(): Sniff
    {
        return new ForbiddenReferenceSniff();
    }
}

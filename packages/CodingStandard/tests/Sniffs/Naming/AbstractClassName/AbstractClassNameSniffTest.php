<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\AbstractClassName;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class AbstractClassNameSniffTest extends AbstractSniffTestCase
{
    /**
     * @dataProvider provideCases()
     */
    public function testFix(string $input, string $expected): void
    {
        $this->doTest($input, $expected);
    }

    /**
     * @return string[][]
     */
    public function provideCases(): array
    {
        return [
            // wrong => fixed
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/wrong/wrong-fixed.php.inc']
        ];
    }

    protected function createSniff(): Sniff
    {
        return new AbstractClassNameSniff();
    }
}

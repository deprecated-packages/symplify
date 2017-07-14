<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\DependencyInjection;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Test\AbstractFixerTestCase;
use Symplify\CodingStandard\Fixer\DependencyInjection\InjectToConstructorInjectionFixer;

final class InjectToConstructorInjectionFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public function provideFixCases(): array
    {
        return [
            // properties with @inject annotation
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong.php.inc'),
//            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed3.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong3.php.inc'),
            ],
            // methods named inject<*>()
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed2.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc'),
//            ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new InjectToConstructorInjectionFixer;
    }
}

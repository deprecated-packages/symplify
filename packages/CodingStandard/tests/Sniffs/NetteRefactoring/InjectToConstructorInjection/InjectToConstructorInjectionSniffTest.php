<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\NetteRefactoring\InjectToConstructorInjection;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Tests\Sniffs\SniffRunner;
use SymplifyCodingStandard\Sniffs\NetteRefactoring\InjectToConstructorInjectionSniff;

/**
 * Constructor injection should be used over @inject annotation and inject* methods.
 * Except abstract BasePresenter.
 */
final class InjectToConstructorInjectionSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(InjectToConstructorInjectionSniff::class, __DIR__);
    }


//    public function testDetection()
//    {
//        $this->codeSnifferRunner = new SniffRunner(InjectToConstructorInjectionSniff::class);

        //        $this->assertSame(0, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php.inc'));
//        $this->assertSame(0, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php.inc'));
//        $this->assertSame(0, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct3.php.inc'));
//        $this->assertSame(1, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php.inc'));
//        $this->assertSame(1, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php.inc'));
//    }

//    public function testFixing()
//    {
//        $fixedContent = $this->codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);

//        $fixedContent = $this->codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);
//    }
}

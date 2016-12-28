<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\NetteRefactoring\InjectToConstructorInjection;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;

/**
 * Constructor injection should be used over @inject annotation and inject* methods.
 * Except abstract BasePresenter.
 */
final class InjectToConstructorInjectionSniffTest extends TestCase
{
    /**
     * @var CodeSnifferRunner
     */
    private $codeSnifferRunner;

    protected function setUp()
    {
        $this->codeSnifferRunner = new CodeSnifferRunner(
            'SymplifyCodingStandard.NetteRefactoring.InjectToConstructorInjection'
        );
    }

    public function testDetection()
    {
//        $this->assertSame(0, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php.inc'));
//        $this->assertSame(0, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php.inc'));
//        $this->assertSame(0, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct3.php.inc'));
//        $this->assertSame(1, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php.inc'));
//        $this->assertSame(1, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php.inc'));
    }

    public function testFixing()
    {
//        $fixedContent = $this->codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);

        $fixedContent = $this->codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);
    }
}

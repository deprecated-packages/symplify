<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\WhiteSpace\IfElseTryCatchFinally;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\WhiteSpace\IfElseTryCatchFinallySniff;


final class IfElseTryCatchFinallySniffTest extends TestCase
{

	public function testDetection()
	{
		$codeSnifferRunner = new CodeSnifferRunner(IfElseTryCatchFinallySniff::NAME);

		$this->assertSame(3, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
		$this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
	}


	public function testFixing()
	{
		$codeSnifferRunner = new CodeSnifferRunner(IfElseTryCatchFinallySniff::NAME);

		$fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php');
		$this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);
	}

}

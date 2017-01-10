<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructures\NewClass;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\ControlStructures\NewClassSniff;


final class NewClassSniffTest extends TestCase
{

	public function testDetection()
	{
		$codeSnifferRunner = new CodeSnifferRunner(NewClassSniff::NAME);

		$this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
		$this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
	}


	public function testFixing()
	{
		$codeSnifferRunner = new CodeSnifferRunner(NewClassSniff::NAME);

		$fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php');
		$this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);
	}

}

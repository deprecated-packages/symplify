<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Runner;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Contract\Runner\RunnerInterface;
use Symplify\CodingStandard\Runner\Psr2Runner;

final class Psr2RunnerTest extends TestCase
{
    /**
     * @var RunnerInterface
     */
    private $runner;

    protected function setUp()
    {
        $this->runner = new Psr2Runner('inc');
    }

    public function testRunForDirectory()
    {
        $this->assertFalse($this->runner->hasErrors());

        $output = $this->runner->runForDirectory(__DIR__ . '/Psr2RunnerSource');

        $this->assertContains('PSR2.Classes.ClassDeclaration.OpenBraceNewLine', $output);
        $this->assertTrue($this->runner->hasErrors());
    }

    public function testFixDirectory()
    {
        $filePath = __DIR__ . '/Psr2RunnerSource/SomeClass.php.inc';
        $fileBackup = file_get_contents($filePath);

        $this->runner->fixDirectory(__DIR__ . '/Psr2RunnerSource');
        $fixedFile = file_get_contents($filePath);
        $this->assertNotSame($fixedFile, $fileBackup);

        file_put_contents($filePath, $fileBackup);
    }
}

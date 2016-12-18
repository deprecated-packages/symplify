<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Runner;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Contract\Runner\RunnerInterface;
use Symplify\CodingStandard\Runner\SymplifyRunner;

final class SymplifyRunnerTest extends TestCase
{
    /**
     * @var RunnerInterface
     */
    private $runner;

    protected function setUp()
    {
        $this->runner = new SymplifyRunner('inc');
    }

    public function testRunForDirectory()
    {
        $this->assertFalse($this->runner->hasErrors());

        $output = $this->runner->runForDirectory(__DIR__ . '/SymplifyRunnerSource');

        $this->assertTrue($this->runner->hasErrors());
        $this->assertContains('SymplifyCodingStandard.Classes.FinalInterface', $output);
    }

    public function testFixDirectory()
    {
        $filePath = __DIR__ . '/SymplifyRunnerSource/SomeClass.php.inc';
        $fileBackup = file_get_contents($filePath);

        $this->runner->fixDirectory(__DIR__ . '/SymplifyRunnerSource');
        $fixedFile = file_get_contents($filePath);
        $this->assertNotSame($fixedFile, $fileBackup);

        file_put_contents($filePath, $fileBackup);
    }
}

<?php

namespace Symplify\CodingStandard\Tests\Runner;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Runner\ContribRunner;

final class ContribRunnerTest extends TestCase
{
    /**
     * @var ContribRunner
     */
    private $runner;

    protected function setUp()
    {
        $this->runner = new ContribRunner();
    }

    public function testRunForDirectory()
    {
        $output = $this->runner->runForDirectory(__DIR__.'/ContribRunnerSource');

        $this->assertStringMatchesFormat(
            file_get_contents(__DIR__.'/ContribRunnerSource/expected.txt'),
            $output
        );
    }

    public function testHasErrors()
    {
        $this->assertFalse($this->runner->hasErrors());
        $this->runner->runForDirectory(__DIR__.'/ContribRunnerSource');

        $this->assertFalse($this->runner->hasErrors());
    }

    public function testFixDirectory()
    {
        $filePath = __DIR__.'/ContribRunnerSource/SomeClass.php.inc';
        $fileBackup = file_get_contents($filePath);

        $this->runner->fixDirectory(__DIR__.'/ContribRunnerSource');
        $fixedFile = file_get_contents($filePath);
        $this->assertSame($fixedFile, $fileBackup);

        file_put_contents($filePath, $fileBackup);
    }
}

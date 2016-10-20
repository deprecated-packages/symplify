<?php

namespace Symplify\CodingStandard\Tests\Runner;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Runner\SymfonyRunner;

final class SymfonyRunnerTest extends TestCase
{
    /**
     * @var SymfonyRunner
     */
    private $runner;

    protected function setUp()
    {
        $this->runner = new SymfonyRunner();
    }

    public function testRunForDirectory()
    {
        $output = $this->runner->runForDirectory(__DIR__.'/SymfonyRunnerSource');

        $this->assertStringMatchesFormat(
            file_get_contents(__DIR__.'/SymfonyRunnerSource/expected.txt'),
            $output
        );
    }

    public function testHasErrors()
    {
        $this->assertFalse($this->runner->hasErrors());
        $this->runner->runForDirectory(__DIR__.'/SymfonyRunnerSource');

        $this->assertFalse($this->runner->hasErrors());
    }

    public function testFixDirectory()
    {
        $filePath = __DIR__.'/SymfonyRunnerSource/SomeClass.php.inc';
        $fileBackup = file_get_contents($filePath);

        $this->runner->fixDirectory(__DIR__.'/SymfonyRunnerSource');
        $fixedFile = file_get_contents($filePath);
        $this->assertSame($fixedFile, $fileBackup);

        file_put_contents($filePath, $fileBackup);
    }
}

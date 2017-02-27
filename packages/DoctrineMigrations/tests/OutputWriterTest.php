<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Zenify\DoctrineMigrations\OutputWriter;

final class OutputWriterTest extends TestCase
{
    /**
     * @var OutputWriter
     */
    private $outputWriter;

    protected function setUp(): void
    {
        $this->outputWriter = new OutputWriter;
    }

    public function testGetOutputWriterWhenNeeded(): void
    {
        $consoleOutput = Assert::getObjectAttribute($this->outputWriter, 'consoleOutput');
        $this->assertNull($consoleOutput);

        $this->outputWriter->write('');

        $consoleOutput = Assert::getObjectAttribute($this->outputWriter, 'consoleOutput');
        $this->assertInstanceOf(ConsoleOutput::class, $consoleOutput);
    }
}

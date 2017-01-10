<?php

declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Symfony\Component\Console\Output\ConsoleOutput;
use Zenify\DoctrineMigrations\OutputWriter;

final class OutputWriterTest extends TestCase
{

    /**
     * @var OutputWriter
     */
    private $outputWriter;


    protected function setUp()
    {
        $this->outputWriter = new OutputWriter;
    }


    public function testGetOutputWriterWhenNeeded()
    {
        $consoleOutput = PHPUnit_Framework_Assert::getObjectAttribute($this->outputWriter, 'consoleOutput');
        $this->assertNull($consoleOutput);

        $this->outputWriter->write('');

        $consoleOutput = PHPUnit_Framework_Assert::getObjectAttribute($this->outputWriter, 'consoleOutput');
        $this->assertInstanceOf(ConsoleOutput::class, $consoleOutput);
    }
}

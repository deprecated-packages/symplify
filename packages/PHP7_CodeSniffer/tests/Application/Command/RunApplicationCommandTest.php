<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Application\Command\RunApplicationCommand;

final class RunApplicationCommandTest extends TestCase
{
    public function testConstructor()
    {
        $command = new RunApplicationCommand(
            $source = ['source'],
            $standards = ['standards'],
            $sniffs = ['sniffs'],
            $excludedSniffs = ['excluded-sniffs'],
            $isFixer = true
        );

        $this->assertSame($excludedSniffs, $command->getExcludedSniffs());
        $this->assertSame($source, $command->getSource());
        $this->assertSame($standards, $command->getStandards());
        $this->assertSame($sniffs, $command->getSniffs());
        $this->assertSame($excludedSniffs, $command->getExcludedSniffs());
        $this->assertSame($isFixer, $command->isFixer());
    }
}

<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Application\Application;
use Symplify\PHP7_CodeSniffer\Application\Command\RunApplicationCommand;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->application = Instantiator::createApplication();
    }

    public function testRunCommand()
    {
        $this->application->runCommand($this->createCommand());
    }

    private function createCommand() : RunApplicationCommand
    {
        return new RunApplicationCommand(
            $source = [__DIR__ . '/ApplicationSource'],
            $standards = ['PSR2'],
            $sniffs = [],
            $excludedSniffs = [],
            $isFixer = true
        );
    }
}

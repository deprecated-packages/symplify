<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Console;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Console\ConsoleApplication;

final class ConsoleApplicationTest extends TestCase
{
    /**
     * @var ConsoleApplication
     */
    private $consoleApplication;

    protected function setUp()
    {
        $this->consoleApplication = new ConsoleApplication();
    }

    public function testGetLongVersion()
    {
        $this->assertSame(
            '<info>Sculpin - Static Site Generator</info>',
            $this->consoleApplication->getLongVersion()
        );
    }

    public function testGetDefaultOptions()
    {
        $definition = $this->consoleApplication->getDefinition();
        $this->assertSame(1, $definition->getArgumentCount());
        $this->assertTrue($definition->hasOption('help'));
        $this->assertTrue($definition->hasOption('version'));
    }
}

<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Console;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Console\ConsoleApplication;

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
    }
}

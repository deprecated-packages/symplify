<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Application\Command\RunCommand;

final class RunCommandTest extends TestCase
{
    public function test()
    {
        $command = new RunCommand(true, 'source', 'output');
        $this->assertTrue($command->isRunServer());
        $this->assertSame('source', $command->getSourceDirectory());
        $this->assertSame('output', $command->getOutputDirectory());
    }
}

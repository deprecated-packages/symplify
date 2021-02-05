<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Symplify\GitWrapper\GitCommand;

final class GitCommandTest extends AbstractGitWrapperTestCase
{
    public function testCommand(): void
    {
        $command = $this->randomString();
        $argument = $this->randomString();
        $flag = $this->randomString();
        $optionName = $this->randomString();
        $optionValue = $this->randomString();

        $gitCommand = new GitCommand($command);
        $gitCommand->addArgument($argument);
        $gitCommand->setFlag($flag);
        $gitCommand->setOption($optionName, $optionValue);

        $expected = [$command, sprintf('--%s', $flag), sprintf('--%s', $optionName), $optionValue, $argument];

        $this->assertSame($expected, $gitCommand->getCommandLine());
    }

    public function testMultiOption(): void
    {
        $gitCommand = new GitCommand('test-command');
        $gitCommand->setOption('test-arg', [true, true]);

        $expected = ['test-command', '--test-arg', '--test-arg'];
        $commandLine = $gitCommand->getCommandLine();

        $this->assertSame($expected, $commandLine);
    }
}

<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Symplify\GitWrapper\GitCommand;

final class GitCommandTest extends AbstractGitWrapperTestCase
{
    public function testCommand(): void
    {
        $command = 'command';
        $argument = 'argument';
        $flag = 'flag';
        $optionName = 'optionName';
        $optionValue = 'optionValue';

        $gitCommand = new GitCommand($command, $argument);
        $gitCommand->setFlag($flag);
        $gitCommand->setOption($optionName, $optionValue);

        $expected = [
            "${command}",
            "--${flag}",
            "--${optionName}=${optionValue}",
            "${argument}"
        ];

        $this->assertSame($expected, $gitCommand->getCommandLineItems());
    }

    public function testOption(): void
    {
        $optionName = 'optionName';
        $optionValue = 'optionValue';

        $git = new GitCommand('', [$optionName => $optionValue]);
        $this->assertSame($optionValue, $git->getOption($optionName));

        $git->unsetOption($optionName);
        $this->assertNull($git->getOption($optionName));
    }

    /**
     * @see https://github.com/cpliakas/git-wrapper/issues/50
     */
    public function testMultiOption(): void
    {
        $gitCommand = new GitCommand('test-command', ['test-arg' => [true, true]]);
        $this->assertSame(['--test-arg' ,'--test-arg'], $gitCommand->buildOptions());
    }
}

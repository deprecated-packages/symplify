<?php declare(strict_types=1);

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

        $git = new GitCommand($command, $argument);
        $git->setFlag($flag);
        $git->setOption($optionName, $optionValue);

        $expected = "${command} --${flag} --${optionName}='${optionValue}' '${argument}'";

        $this->assertSame($expected, $git->getCommandLine());
    }

//    public function testOption(): void
//    {
//        $optionName = $this->randomString();
//        $optionValue = $this->randomString();
//
//        $git = new GitCommand(null, [$optionName => $optionValue]);
//
//        $this->assertSame($optionValue, $git->getOption($optionName));
//
//        $git->unsetOption($optionName);
//        $this->assertNull($git->getOption($optionName));
//    }
//
//    /**
//     * @see https://github.com/cpliakas/git-wrapper/issues/50
//     */
//    public function testMultiOption(): void
//    {
//        $git = new GitCommand('test-command', ['test-arg' => [true, true]]);
//
//        $expected = 'test-command --test-arg --test-arg';
//        $commandLine = $git->getCommandLine();
//
//        $this->assertSame($expected, $commandLine);
//    }
}

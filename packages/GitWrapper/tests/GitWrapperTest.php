<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;
use Symplify\GitWrapper\Tests\Event\TestDispatcher;

final class GitWrapperTest extends AbstractGitWrapperTestCase
{
    public function testGitBinary(): void
    {
        $this->gitWrapper->setGitBinary('some/bin/git');
        $this->assertSame('some/bin/git', $this->gitWrapper->getGitBinary());
    }

    public function testDispatcher(): void
    {
        $dispatcher = new TestDispatcher();
        $this->gitWrapper->setDispatcher($dispatcher);
        $this->assertSame($dispatcher, $this->gitWrapper->getDispatcher());
    }

    public function testTimeout(): void
    {
        $timeout = random_int(1, 60);
        $this->gitWrapper->setTimeout($timeout);
        $this->assertSame($timeout, $this->gitWrapper->getTimeout());
    }

    public function testEnvVar(): void
    {
        $var = $this->randomString();
        $value = $this->randomString();

        $this->gitWrapper->setEnvVar($var, $value);
        $this->assertSame($value, $this->gitWrapper->getEnvVar($var));

        $envVars = $this->gitWrapper->getEnvVars();
        $this->assertSame($value, $envVars[$var]);

        $this->gitWrapper->unsetEnvVar($var);
        $this->assertNull($this->gitWrapper->getEnvVar($var));
    }

    public function testEnvVarDefault(): void
    {
        $var = $this->randomString();
        $default = $this->randomString();
        $this->assertSame($default, $this->gitWrapper->getEnvVar($var, $default));
    }

    public function testGitVersion(): void
    {
        $version = $this->gitWrapper->version();
        $this->assertGitVersion($version);
    }

    public function testSetPrivateKey(): void
    {
        $sshWrapperExpected = dirname(__DIR__) . '/bin/git-ssh-wrapper.sh';

        $this->gitWrapper->setPrivateKey(__DIR__ . '/id_rsa');
        $this->assertSame(__DIR__ . '/id_rsa', $this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH_KEY));
        $this->assertSame(22, $this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH_PORT));
        $this->assertSame($sshWrapperExpected, $this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH));
    }

    public function testSetPrivateKeyPort(): void
    {
        $port = random_int(1024, 10000);
        $this->gitWrapper->setPrivateKey(__DIR__ . '/id_rsa', $port);
        $this->assertSame($port, $this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH_PORT));
    }

    public function testSetPrivateKeyWrapper(): void
    {
        $sshWrapper = __DIR__ . '/dummy-wrapper.sh';
        $sshWrapperExpected = realpath($sshWrapper);
        $this->gitWrapper->setPrivateKey(__DIR__ . '/id_rsa', 22, $sshWrapper);
        $this->assertSame($sshWrapperExpected, $this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH));
    }

    public function testSetPrivateKeyError(): void
    {
        $this->expectException(GitException::class);

        $badKey = './test/id_rsa_bad';
        $this->gitWrapper->setPrivateKey($badKey);
    }

    public function testSetPrivateKeyWrapperError(): void
    {
        $this->expectException(GitException ::class);

        $badWrapper = __DIR__  . '/dummy-wrapper-bad.sh';
        $this->gitWrapper->setPrivateKey(__DIR__ . '/id_rsa', 22, $badWrapper);
    }

    public function testUnsetPrivateKey(): void
    {
        // Set and unset the private key.
        $key = __DIR__ . '/id_rsa';
        $sshWrapper = __DIR__ . '/dummy-wrapper.sh';
        $this->gitWrapper->setPrivateKey($key, 22, $sshWrapper);
        $this->gitWrapper->unsetPrivateKey();

        $this->assertNull($this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH_KEY));
        $this->assertNull($this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH_PORT));
        $this->assertNull($this->gitWrapper->getEnvVar(GitWrapper::ENV_GIT_SSH));
    }

    public function testGitCommand(): void
    {
        $version = $this->gitWrapper->git('--version');
        $this->assertGitVersion($version);
    }

    public function testGitCommandError(): void
    {
        $this->expectException(GitException::class);

        $this->runBadCommand();
    }

    public function testGitRun(): void
    {
        $command = new GitCommand;
        $command->setFlag('version');
        $command->setDirectory(__DIR__ . '/../tests'); // Directory just has to exist.
        $version = $this->gitWrapper->run($command);
        $this->assertGitVersion($version);
    }

    public function testGitRunDirectoryError(): void
    {
        $this->expectException(GitException::class);

        $command = new GitCommand;
        $command->setFlag('version');
        $command->setDirectory('/some/bad/directory');
        $this->gitWrapper->run($command);
    }

//    public function testWrapperExecutable(): void
//    {
//        $sshWrapper = realpath(__DIR__ . '/../../../bin/git-ssh-wrapper.sh');
//        $this->assertTrue(is_executable($sshWrapper));
//    }
//
//    public function testWorkingCopy(): void
//    {
//        $directory = './' . $this->randomString();
//        $git = $this->gitWrapper->workingCopy($directory);
//
//        $this->assertTrue($git instanceof GitWorkingCopy);
//        $this->assertSame($directory, $git->getDirectory());
//        $this->assertSame($this->gitWrapper, $git->getWrapper());
//    }
//
//    public function testParseRepositoryName(): void
//    {
//        $nameGit = GitWrapper::parseRepositoryName('git@github.com:cpliakas/git-wrapper.git');
//        $this->assertSame($nameGit, 'git-wrapper');
//
//        $nameHttps = GitWrapper::parseRepositoryName('https://github.com/cpliakas/git-wrapper.git');
//        $this->assertSame($nameHttps, 'git-wrapper');
//    }
//
//    public function testCloneWothoutDirectory(): void
//    {
//        $this->gitWrapper->cloneRepository('file:///' . $this->randomString());
//    }
}

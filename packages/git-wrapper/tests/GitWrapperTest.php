<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWorkingCopy;

final class GitWrapperTest extends AbstractGitWrapperTestCase
{
    /**
     * @var string
     */
    private const BINARY = '/path/to/binary';

    /**
     * @var string
     */
    private const BAD_KEY = './tests/id_rsa_bad';

    /**
     * @var string
     */
    private const BAD_WRAPPER = './tests/dummy-wrapper-bad.sh';

    public function testSetGitBinary(): void
    {
        $this->gitWrapper->setGitBinary(self::BINARY);
        $this->assertSame(self::BINARY, $this->gitWrapper->getGitBinary());
    }

    public function testSetTimeout(): void
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
        $envVar = $this->gitWrapper->getEnvVar($var);
        $this->assertSame($value, $envVar);

        $envVars = $this->gitWrapper->getEnvVars();
        $this->assertSame($value, $envVars[$var]);

        $this->gitWrapper->unsetEnvVar($var);
        $unsetEnvVar = $this->gitWrapper->getEnvVar($var);
        $this->assertNull($unsetEnvVar);
    }

    public function testEnvVarDefault(): void
    {
        $var = $this->randomString();
        $default = $this->randomString();

        $randomEnvVar = $this->gitWrapper->getEnvVar($var, $default);
        $this->assertSame($default, $randomEnvVar);
    }

    public function testGitVersion(): void
    {
        $version = $this->gitWrapper->version();
        $this->assertGitVersion($version);
    }

    public function testSetPrivateKey(): void
    {
        $key = __DIR__ . '/../tests/id_rsa';
        $keyExpected = realpath($key);
        $sshWrapperExpected = realpath(__DIR__ . '/../bin/git-ssh-wrapper.sh');

        $this->gitWrapper->setPrivateKey($key);

        $sshKeyEnvVar = $this->gitWrapper->getEnvVar('GIT_SSH_KEY');
        $this->assertSame($keyExpected, $sshKeyEnvVar);

        $sshPortEnvVar = $this->gitWrapper->getEnvVar('GIT_SSH_PORT');
        $this->assertSame(22, $sshPortEnvVar);

        $sshEnvVar = $this->gitWrapper->getEnvVar('GIT_SSH');
        $this->assertSame($sshWrapperExpected, $sshEnvVar);
    }

    public function testSetPrivateKeyPort(): void
    {
        $port = random_int(1024, 10000);
        $this->gitWrapper->setPrivateKey(__DIR__ . '/../tests/id_rsa', $port);

        $sshPortEnvVar = $this->gitWrapper->getEnvVar('GIT_SSH_PORT');
        $this->assertSame($port, $sshPortEnvVar);
    }

    public function testSetPrivateKeyWrapper(): void
    {
        $sshWrapper = __DIR__ . '/../tests/dummy-wrapper.sh';
        $sshWrapperExpected = realpath($sshWrapper);
        $this->gitWrapper->setPrivateKey(__DIR__ . '/../tests/id_rsa', 22, $sshWrapper);

        $sshEnvVar = $this->gitWrapper->getEnvVar('GIT_SSH');
        $this->assertSame($sshWrapperExpected, $sshEnvVar);
    }

    public function testSetPrivateKeyError(): void
    {
        $this->expectException(GitException::class);
        $this->gitWrapper->setPrivateKey(self::BAD_KEY);
    }

    public function testSetPrivateKeyWrapperError(): void
    {
        $this->expectException(GitException::class);
        $this->gitWrapper->setPrivateKey('./tests/id_rsa', 22, self::BAD_WRAPPER);
    }

    public function testUnsetPrivateKey(): void
    {
        // Set and unset the private key.
        $key = __DIR__ . '/../tests/id_rsa';
        $sshWrapper = __DIR__ . '/../tests/dummy-wrapper.sh';
        $this->gitWrapper->setPrivateKey($key, 22, $sshWrapper);
        $this->gitWrapper->unsetPrivateKey();

        $this->assertNull($this->gitWrapper->getEnvVar('GIT_SSH_KEY'));
        $this->assertNull($this->gitWrapper->getEnvVar('GIT_SSH_PORT'));
        $this->assertNull($this->gitWrapper->getEnvVar('GIT_SSH'));
    }

    public function testGitCommand(): void
    {
        $version = $this->gitWrapper->git('--version');
        $this->assertGitVersion($version);
    }

    public function testGitCommandWithMultipleArguments(): void
    {
        $options = $this->gitWrapper->git('--version --build-options');
        $this->assertNotEmpty($options);
    }

    public function testGitCommandError(): void
    {
        $this->expectException(GitException::class);
        $this->runBadCommand();
    }

    public function testGitRun(): void
    {
        $gitCommand = new GitCommand();
        $gitCommand->setFlag('version');
        // Directory has to exist
        $gitCommand->setDirectory('./tests');

        $version = $this->gitWrapper->run($gitCommand);
        $this->assertGitVersion($version);
    }

    public function testGitRunDirectoryError(): void
    {
        $this->expectException(GitException::class);

        $gitCommand = new GitCommand();
        $gitCommand->setFlag('version');
        $gitCommand->setDirectory('/some/bad/directory');

        $this->gitWrapper->run($gitCommand);
    }

    public function testWrapperExecutable(): void
    {
        $sshWrapper = __DIR__ . '/../bin/git-ssh-wrapper.sh';

        $isExecutable = is_executable($sshWrapper);
        $this->assertTrue($isExecutable);
    }

    public function testWorkingCopy(): void
    {
        $directory = './' . $this->randomString();

        $gitWorkingCopy = $this->gitWrapper->workingCopy($directory);

        $this->assertInstanceOf(GitWorkingCopy::class, $gitWorkingCopy);
        $this->assertSame($directory, $gitWorkingCopy->getDirectory());
        $this->assertSame($this->gitWrapper, $gitWorkingCopy->getWrapper());
    }

    public function testCloneWithoutDirectory(): void
    {
        $this->createRegisterAndReturnBypassEventSubscriber();
        $gitWorkingCopy = $this->gitWrapper->cloneRepository('file:///' . $this->randomString());
        $this->assertTrue($gitWorkingCopy->isCloned());
    }
}

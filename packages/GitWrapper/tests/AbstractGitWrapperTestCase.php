<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Nette\Utils\Random;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitWrapper;

abstract class AbstractGitWrapperTestCase extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    protected const REPO_DIR = __DIR__ . '/temp/repository';

    /**
     * @var string
     */
    protected const WORKING_DIR = __DIR__ . '/temp/working-dir';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var GitWrapper
     */
    protected $gitWrapper;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->gitWrapper = $this->container->get(GitWrapper::class);
    }

    /**
     * The version returned by the `git --version` command.
     */
    public function assertGitVersion(string $type): void
    {
        $match = preg_match('/^git version [.0-9]+/', $type);
        $this->assertNotEmpty($match);
    }

    public function runBadCommand(bool $catchException = false): void
    {
        try {
            $this->gitWrapper->git('a-bad-command');
        } catch (GitException $gitException) {
            if (! $catchException) {
                throw $gitException;
            }
        }
    }

    protected function randomString(): string
    {
        return Random::generate();
    }
}

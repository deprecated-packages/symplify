<?php declare(strict_types=1);

namespace Symplify\Statie\Github;

use GitWrapper\GitWrapper;
use Symplify\Statie\Utils\FilesystemChecker;

final class GithubPublishingProcess
{
    /**
     * @var string
     */
    private const CONFIG_EMAIL = 'travis@travis-ci.org';

    /**
     * @var string
     */
    private const CONFIG_NAME = 'Travis';

    /**
     * @var FilesystemChecker
     */
    private $filesystemChecker;

    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    public function __construct(FilesystemChecker $filesystemChecker, GitWrapper $gitWrapper)
    {
        $this->filesystemChecker = $filesystemChecker;
        $this->gitWrapper = $gitWrapper;
    }

    public function pushDirectoryContentToRepository(
        string $outputDirectory,
        string $githubRepository,
        string $branch
    ): void {
        $this->filesystemChecker->ensureDirectoryExists($outputDirectory);

        $git = $this->gitWrapper->init($outputDirectory);

        if (getenv('TRAVIS')) {
            $git->config('user.email', self::CONFIG_EMAIL);
            $git->config('user.name', self::CONFIG_NAME);
        }

        $git->checkout($branch, [
            'orphan' => true,
        ]);
        $git->add('.');
        $git->commit('Regenerate output');
        $git->addRemote('origin', $githubRepository);
        $git->push('origin', $branch, [
            'force' => true,
            'quiet' => true,
        ]);
    }
}

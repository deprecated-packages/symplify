<?php declare(strict_types=1);

namespace Symplify\Statie\Github;

use GitWrapper\GitWrapper;
use Symplify\Statie\Utils\FilesystemChecker;

final class GihubPublishingProcess
{
    /**
     * @var string
     */
    private const CONFIG_EMAIL = 'travis@travis-ci.org';

    /**
     * @var string
     */
    private const CONFIG_NAME = 'Travis';

    public function pushDirectoryContentToRepository(string $outputDirectory, string $githubRepository)
    {
        FilesystemChecker::ensureDirectoryExists($outputDirectory);

        $git = (new GitWrapper())->init($outputDirectory);

        if (getenv('TRAVIS')) {
            $git->config('user.email', self::CONFIG_EMAIL);
            $git->config('user.name', self::CONFIG_NAME);
        }

        $git->checkout('gh-pages', [
            'orphan' => true,
        ]);
        $git->add('.');
        $git->commit('Regenerate output');
        $git->addRemote('origin', $githubRepository);
        $git->push('origin', 'gh-pages', [
            'force' => true,
            'quiet' => true,
        ]);
    }
}

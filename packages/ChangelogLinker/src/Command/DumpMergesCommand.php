<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\Github\GithubApi;
use Symplify\ChangelogLinker\Regex\RegexPattern;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

/**
 * @inspired by https://github.com/weierophinney/changelog_generator
 */
final class DumpMergesCommand extends Command
{
    /**
     * @var string[]
     */
    private $authorsToIgnore = [];

    /**
     * @var GithubApi
     */
    private $githubApi;

    /**
     * @param string[] $authorsToIgnore
     */
    public function __construct(array $authorsToIgnore, GithubApi $githubApi)
    {
        $this->authorsToIgnore = $authorsToIgnore;
        $this->githubApi = $githubApi;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            'Scans repository merged PRs, that are not in the CHANGELOG.md yet, and dumps them in changelog format.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lastIdInChangelog = $this->getLastIdInChangelog();

        $pullRequests = $this->githubApi->getClosedPullRequestsSinceId($lastIdInChangelog);

        foreach ($pullRequests as $pullRequest) {
            $pullRequestMessage = sprintf('- [#%s] %s', $pullRequest['number'], $pullRequest['title']);
            $pullRequestAuthor = $pullRequest['user']['login'];

            // skip the main maintainer to prevent self-thanking floods
            if (! in_array($pullRequestAuthor, $this->authorsToIgnore, true)) {
                $pullRequestMessage .= ', Thanks to @' . $pullRequestAuthor;
            }

            $output->writeln($pullRequestMessage);
        }

        // success
        return 0;
    }

    private function getLastIdInChangelog(): int
    {
        $changelogContent = file_get_contents(getcwd() . '/CHANGELOG.md');

        $match = Strings::match($changelogContent, '#' . RegexPattern::PR_OR_ISSUE . '#');
        if ($match) {
            return (int) $match['id'];
        }

        return 1;
    }
}

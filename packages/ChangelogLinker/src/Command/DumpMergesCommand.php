<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeTree;
use Symplify\ChangelogLinker\Configuration\Configuration;
use Symplify\ChangelogLinker\Github\GithubApi;
use Symplify\ChangelogLinker\Regex\RegexPattern;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

/**
 * @inspired by https://github.com/weierophinney/changelog_generator
 */
final class DumpMergesCommand extends Command
{
    /**
     * @var string
     */
    private const OPTION_IN_CATEGORIES = 'in-categories';

    /**
     * @var string
     */
    private const OPTION_IN_PACKAGES = 'in-packages';

    /**
     * @var GithubApi
     */
    private $githubApi;

    /**
     * @var ChangeTree
     */
    private $changeTree;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Configuration $configuration,
        GithubApi $githubApi,
        ChangeTree $changeTree,
        SymfonyStyle $symfonyStyle
    ) {
        $this->githubApi = $githubApi;
        $this->changeTree = $changeTree;
        $this->symfonyStyle = $symfonyStyle;
        $this->configuration = $configuration;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            'Scans repository merged PRs, that are not in the CHANGELOG.md yet, and dumps them in changelog format.'
        );
        $this->addOption(
            self::OPTION_IN_CATEGORIES,
            null,
            InputOption::VALUE_NONE,
            'Print in Added/Changed/Fixed/Removed - detected from "Add", "Fix", "Removed" etc. keywords in merge title.'
        );

        $this->addOption(
            self::OPTION_IN_PACKAGES,
            null,
            InputOption::VALUE_NONE,
            'Print in groups in package names - detected from "[PackageName]" in merge title.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lastIdInChangelog = $this->getLastIdInChangelog();

        $pullRequests = $this->githubApi->getClosedPullRequestsSinceId($lastIdInChangelog);

        if (count($pullRequests) === 0) {
            $this->symfonyStyle->note(
                sprintf('There are no new pull requests to be added since ID "%d".', $lastIdInChangelog)
            );

            // success
            return 0;
        }

        $this->loadPullRequestsToChangeTree($pullRequests);

        if (! $input->getOption(self::OPTION_IN_CATEGORIES) && ! $input->getOption(self::OPTION_IN_PACKAGES)) {
            $this->printAllChanges();

            // success
            return 0;
        }

        if ($input->getOption(self::OPTION_IN_CATEGORIES)) {
            // @todo resolve priority
            $sortedChanges = $this->sortChangesByCategoryAndPackage($this->changeTree->getChanges());

            $lastCategory = '';
            foreach ($sortedChanges as $change) {
                if ($lastCategory !== $change->getCategory()) {
                    $this->symfonyStyle->newLine(1);
                    $this->symfonyStyle->writeln('### ' . $change->getCategory());
                    $this->symfonyStyle->newLine(1);
                }

                $this->symfonyStyle->writeln($change->getMessage());

                $lastCategory = $change->getCategory();
            }

            $this->symfonyStyle->newLine(1);
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

    /**
     * @param mixed[] $pullRequests
     */
    private function loadPullRequestsToChangeTree(array $pullRequests): void
    {
        foreach ($pullRequests as $pullRequest) {
            $pullRequestMessage = sprintf('- [#%s] %s', $pullRequest['number'], $pullRequest['title']);
            $pullRequestAuthor = $pullRequest['user']['login'];

            // skip the main maintainer to prevent self-thanking floods
            if (! in_array($pullRequestAuthor, $this->configuration->getAuthorsToIgnore(), true)) {
                $pullRequestMessage .= ', Thanks to @' . $pullRequestAuthor;
            }

            $this->changeTree->addPullRequestMessage($pullRequestMessage);
        }
    }

    private function printAllChanges(): void
    {
        $this->symfonyStyle->newLine(1);

        foreach ($this->changeTree->getChanges() as $change) {
            $this->symfonyStyle->writeln($change->getMessage());
        }

        $this->symfonyStyle->newLine(1);
    }

    /**
     * @param  Change[] $changes
     * @return Change[]
     */
    private function sortChangesByCategoryAndPackage(array $changes): array
    {
        $sort = [];
        foreach ($changes as $key => $change) {
            $sort['category'][] = $change->getCategory();
        }

        foreach ($changes as $key => $change) {
            $sort['package'][] = $change->getPackage();
        }

        array_multisort($sort['category'], $sort['package'], $changes);

        return $changes;
    }
}

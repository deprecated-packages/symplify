<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\ChangeTree\ChangeTree;
use Symplify\ChangelogLinker\Console\Output\DumpMergesReporter;
use Symplify\ChangelogLinker\Github\GithubApi;
use Symplify\ChangelogLinker\Github\PullRequestMessageFactory;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

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
     * @var PullRequestMessageFactory
     */
    private $pullRequestMessageFactory;

    /**
     * @var ChangeSorter
     */
    private $changeSorter;

    /**
     * @var IdsAnalyzer
     */
    private $idsAnalyzer;

    /**
     * @var DumpMergesReporter
     */
    private $dumpMergesReporter;

    public function __construct(
        GithubApi $githubApi,
        ChangeTree $changeTree,
        SymfonyStyle $symfonyStyle,
        PullRequestMessageFactory $pullRequestMessageFactory,
        ChangeSorter $changeSorter,
        IdsAnalyzer $idsAnalyzer,
        DumpMergesReporter $dumpMergesReporter
    ) {
        $this->githubApi = $githubApi;
        $this->changeTree = $changeTree;
        $this->symfonyStyle = $symfonyStyle;
        $this->pullRequestMessageFactory = $pullRequestMessageFactory;
        $this->changeSorter = $changeSorter;
        $this->idsAnalyzer = $idsAnalyzer;
        $this->dumpMergesReporter = $dumpMergesReporter;

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
        $lastIdInChangelog = $this->idsAnalyzer->getLastIdInChangelog(getcwd() . '/CHANGELOG.md');

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
            $this->dumpMergesReporter->reportChanges($this->changeTree->getChanges());

            // success
            return 0;
        }

        $sortPriority = $this->getSortPriority($input);

        $sortedChanges = $this->changeSorter->sortByCategoryAndPackage($this->changeTree->getChanges(), $sortPriority);

        $this->dumpMergesReporter->reportChangesWithHeadlines(
            $sortedChanges,
            $input->getOption(self::OPTION_IN_CATEGORIES),
            $input->getOption(self::OPTION_IN_PACKAGES),
            $sortPriority
        );

        // success
        return 0;
    }

    /**
     * @param mixed[] $pullRequests
     */
    private function loadPullRequestsToChangeTree(array $pullRequests): void
    {
        foreach ($pullRequests as $pullRequest) {
            $pullRequestMessage = $this->pullRequestMessageFactory->createMessageFromPullRequest($pullRequest);
            $this->changeTree->addPullRequestMessage($pullRequestMessage);
        }
    }

    /**
     * Detects the order in which "--in-packages" and "--in-categories" are called.
     * The first has a priority.
     */
    private function getSortPriority(InputInterface $input): string
    {
        $rawOptions = (new PrivatesAccessor())->getPrivateProperty($input, 'options');

        foreach ($rawOptions as $name => $value) {
            if ($name === 'in-packages') {
                return 'packages';
            }

            return 'categories';
        }

        return 'categories';
    }
}

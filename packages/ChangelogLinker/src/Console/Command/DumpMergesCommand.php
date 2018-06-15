<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\Console\Output\DumpMergesReporter;
use Symplify\ChangelogLinker\Github\GithubApi;
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
     * @var string
     */
    private const OPTION_IN_TAGS = 'in-tags';

    /**
     * @var GithubApi
     */
    private $githubApi;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

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

    /**
     * @var ChangeFactory
     */
    private $changeFactory;

    /**
     * @var Change[]
     */
    private $changes = [];

    public function __construct(
        GithubApi $githubApi,
        SymfonyStyle $symfonyStyle,
        ChangeSorter $changeSorter,
        IdsAnalyzer $idsAnalyzer,
        DumpMergesReporter $dumpMergesReporter,
        ChangeFactory $changeFactory
    ) {
        parent::__construct();
        $this->changeFactory = $changeFactory;
        $this->githubApi = $githubApi;
        $this->symfonyStyle = $symfonyStyle;
        $this->changeSorter = $changeSorter;
        $this->idsAnalyzer = $idsAnalyzer;
        $this->dumpMergesReporter = $dumpMergesReporter;
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

        $this->addOption(
            self::OPTION_IN_TAGS,
            null,
            InputOption::VALUE_NONE,
            'Print withs tags - detected from date of merge .'
        );

        $this->addOption('token', 't', InputOption::VALUE_REQUIRED, 'Github Token to overcome request limit.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $highestIdInChangelog = $this->idsAnalyzer->getHighestIdInChangelog(getcwd() . '/CHANGELOG.md');

        // @todo temp
        $highestIdInChangelog = 850;

        if ($input->getOption('token')) {
            $this->githubApi->authorizeToken($input->getOption('token'));
        }

        $pullRequests = $this->githubApi->getClosedPullRequestsSinceId($highestIdInChangelog);

        if (count($pullRequests) === 0) {
            $this->symfonyStyle->note(
                sprintf('There are no new pull requests to be added since ID "%d".', $highestIdInChangelog)
            );

            // success
            return 0;
        }

        foreach ($pullRequests as $pullRequest) {
            $this->changes[] = $this->changeFactory->createFromPullRequest($pullRequest);
        }

        if (! $input->getOption(self::OPTION_IN_CATEGORIES) && ! $input->getOption(self::OPTION_IN_PACKAGES)) {
            $this->dumpMergesReporter->reportChanges($this->changes, $input->getOption(self::OPTION_IN_TAGS));

            // success
            return 0;
        }

        $sortPriority = $this->getSortPriority($input);

        $sortedChanges = $this->changeSorter->sortByCategoryAndPackage($this->changes, $sortPriority);
        $sortedChanges = $this->changeSorter->sortByTags($sortedChanges);

        $this->dumpMergesReporter->reportChangesWithHeadlines(
            $sortedChanges,
            $input->getOption(self::OPTION_IN_CATEGORIES),
            $input->getOption(self::OPTION_IN_PACKAGES),
            $input->getOption(self::OPTION_IN_TAGS),
            $sortPriority
        );

        // success
        return 0;
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

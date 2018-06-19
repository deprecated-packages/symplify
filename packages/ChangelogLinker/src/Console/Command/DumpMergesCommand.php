<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\Configuration\Configuration;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\ChangelogLinker\Console\Input\PriorityResolver;
use Symplify\ChangelogLinker\Console\Output\DumpMergesReporter;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\Github\GithubApi;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

/**
 * @inspired by https://github.com/weierophinney/changelog_generator
 */
final class DumpMergesCommand extends Command
{
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
     * @var ChangelogLinker
     */
    private $changelogLinker;

    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    /**
     * @var PriorityResolver
     */
    private $priorityResolver;

    public function __construct(
        GithubApi $githubApi,
        SymfonyStyle $symfonyStyle,
        ChangeSorter $changeSorter,
        IdsAnalyzer $idsAnalyzer,
        DumpMergesReporter $dumpMergesReporter,
        ChangeFactory $changeFactory,
        ChangelogLinker $changelogLinker,
        ChangelogFileSystem $changelogFileSystem,
        PriorityResolver $priorityResolver
    ) {
        parent::__construct();
        $this->changeFactory = $changeFactory;
        $this->githubApi = $githubApi;
        $this->symfonyStyle = $symfonyStyle;
        $this->changeSorter = $changeSorter;
        $this->idsAnalyzer = $idsAnalyzer;
        $this->dumpMergesReporter = $dumpMergesReporter;
        $this->changelogLinker = $changelogLinker;
        $this->changelogFileSystem = $changelogFileSystem;
        $this->priorityResolver = $priorityResolver;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            'Scans repository merged PRs, that are not in the CHANGELOG.md yet, and dumps them in changelog format.'
        );
        $this->addOption(
            Option::IN_CATEGORIES,
            null,
            InputOption::VALUE_NONE,
            'Print in Added/Changed/Fixed/Removed - detected from "Add", "Fix", "Removed" etc. keywords in merge title.'
        );

        $this->addOption(
            Option::IN_PACKAGES,
            null,
            InputOption::VALUE_NONE,
            'Print in groups in package names - detected from "[PackageName]" in merge title.'
        );

        $this->addOption(
            Option::IN_TAGS,
            null,
            InputOption::VALUE_NONE,
            'Print withs tags - detected from date of merge.'
        );

        $this->addOption(
            Option::DRY_RUN,
            null,
            InputOption::VALUE_NONE,
            'Print out to the output instead of writing directly into CHANGELOG.md.'
        );

        $this->addOption(
            Option::TOKEN,
            null,
            InputOption::VALUE_REQUIRED,
            'Github Token to overcome request limit.'
        );

        $this->addOption(Option::LINKIFY, null, InputOption::VALUE_NONE, 'Decorate content with links.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = $this->changelogFileSystem->readChangelog();

        $highestIdInChangelog = $this->idsAnalyzer->getHighestIdInChangelog($content);

        if ($input->getOption(Option::TOKEN)) {
            $this->githubApi->authorizeWithToken($input->getOption(Option::TOKEN));
        }

        $pullRequests = $this->githubApi->getClosedPullRequestsSinceId($highestIdInChangelog);
        if (count($pullRequests) === 0) {
            $this->symfonyStyle->note(
                sprintf('There are no new pull requests to be added since ID "%d".', $highestIdInChangelog)
            );

            // success
            return 0;
        }

        $changes = [];
        foreach ($pullRequests as $pullRequest) {
            $changes[] = $this->changeFactory->createFromPullRequest($pullRequest);
        }

        $sortPriority = $this->priorityResolver->resolveFromInput($input);

        $sortedChanges = $this->changeSorter->sortByCategoryAndPackage($changes, $sortPriority);
        $sortedChanges = $this->changeSorter->sortByTags($sortedChanges);

        $content = $this->dumpMergesReporter->reportChangesWithHeadlines(
            $sortedChanges,
            $input->getOption(Option::IN_CATEGORIES),
            $input->getOption(Option::IN_PACKAGES),
            $input->getOption(Option::IN_TAGS),
            $sortPriority
        );

        if ($input->getOption(Option::LINKIFY)) {
            $content = $this->changelogLinker->processContent($content);
        }

        if ($input->getOption(Option::DRY_RUN)) {
            $this->symfonyStyle->writeln($content);
        } else {
            $this->changelogFileSystem->addToChangelogOnPlaceholder(
                $content,
                Configuration::CHANGELOG_PLACEHOLDER_TO_WRITE
            );
            $this->symfonyStyle->success('The CHANGELOG.md was updated');
        }

        // success
        return 0;
    }
}

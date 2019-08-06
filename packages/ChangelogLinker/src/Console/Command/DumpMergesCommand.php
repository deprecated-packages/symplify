<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;
use Symplify\ChangelogLinker\ChangelogDumper;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\ChangeTree\ChangeResolver;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\ChangelogLinker\Console\Input\PriorityResolver;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystemGuard;
use Symplify\ChangelogLinker\Github\GithubApi;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

/**
 * @inspired by https://github.com/weierophinney/changelog_generator
 */
final class DumpMergesCommand extends Command
{
    /**
     * @inspiration markdown comment: https://gist.github.com/jonikarppinen/47dc8c1d7ab7e911f4c9#gistcomment-2109856
     * @var string
     */
    private const CHANGELOG_PLACEHOLDER_TO_WRITE = '<!-- changelog-linker -->';

    /**
     * @var GithubApi
     */
    private $githubApi;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var IdsAnalyzer
     */
    private $idsAnalyzer;

    /**
     * @var ChangelogDumper
     */
    private $changelogDumper;

    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    /**
     * @var PriorityResolver
     */
    private $priorityResolver;

    /**
     * @var ChangeResolver
     */
    private $changeResolver;

    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    /**
     * @var ChangelogFileSystemGuard
     */
    private $changelogFileSystemGuard;

    public function __construct(
        GithubApi $githubApi,
        SymfonyStyle $symfonyStyle,
        IdsAnalyzer $idsAnalyzer,
        ChangelogDumper $changelogDumper,
        ChangelogLinker $changelogLinker,
        ChangelogFileSystem $changelogFileSystem,
        PriorityResolver $priorityResolver,
        ChangeResolver $changeResolver,
        ChangelogFileSystemGuard $changelogFileSystemGuard
    ) {
        parent::__construct();
        $this->githubApi = $githubApi;
        $this->symfonyStyle = $symfonyStyle;
        $this->idsAnalyzer = $idsAnalyzer;
        $this->changelogDumper = $changelogDumper;
        $this->changelogLinker = $changelogLinker;
        $this->changelogFileSystem = $changelogFileSystem;
        $this->priorityResolver = $priorityResolver;
        $this->changeResolver = $changeResolver;
        $this->changelogFileSystemGuard = $changelogFileSystemGuard;
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
            Option::DRY_RUN,
            null,
            InputOption::VALUE_NONE,
            'Print out to the output instead of writing directly into CHANGELOG.md.'
        );

        $this->addOption(
            Option::SINCE_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'Include pull-request with provided ID and higher. The ID is detected in CHANGELOG.md otherwise.'
        );

        $this->addOption(
            Option::BASE_BRANCH,
            null,
            InputOption::VALUE_OPTIONAL,
            'Base branch towards which the pull requests are targeted'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = $this->changelogFileSystem->readChangelog();

        $this->changelogFileSystemGuard->ensurePlaceholderIsPresent($content, self::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $sinceId = $this->getSinceIdFromInputAndContent($input, $content) ?: 1;
        $pullRequests = $this->githubApi->getMergedPullRequestsSinceId(
            $sinceId,
            $input->getOption(Option::BASE_BRANCH)
        );
        if (count($pullRequests) === 0) {
            $this->symfonyStyle->note(
                sprintf('There are no new pull requests to be added since ID "%d".', $sinceId)
            );

            return ShellCode::SUCCESS;
        }

        $sortPriority = $this->priorityResolver->resolveFromInput($input);
        $changes = $this->changeResolver->resolveSortedChangesFromPullRequestsWithSortPriority(
            $pullRequests,
            $sortPriority
        );

        $content = $this->changelogDumper->reportChangesWithHeadlines(
            $changes,
            (bool) $input->getOption(Option::IN_CATEGORIES),
            (bool) $input->getOption(Option::IN_PACKAGES),
            $sortPriority
        );

        if ($input->getOption(Option::DRY_RUN)) {
            $content = $this->changelogLinker->processContentWithLinkAppends($content);

            $this->symfonyStyle->writeln($content);

            return ShellCode::SUCCESS;
        }

        $content = $this->changelogLinker->processContent($content);

        $this->changelogFileSystem->addToChangelogOnPlaceholder($content, self::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $this->symfonyStyle->success('The CHANGELOG.md was updated');

        return ShellCode::SUCCESS;
    }

    private function getSinceIdFromInputAndContent(InputInterface $input, string $content): ?int
    {
        /** @var string|int|null $sinceId */
        $sinceId = $input->getOption(Option::SINCE_ID);
        if ($sinceId !== null) {
            return (int) $sinceId;
        }

        $baseBranch = $input->getOption(Option::BASE_BRANCH);
        if ($baseBranch !== null) {
            return $this->findHighestIdMergedInBranch($content, $baseBranch);
        }

        return $this->idsAnalyzer->getHighestIdInChangelog($content);
    }

    /**
     * @param string $content
     * @param string $branch
     * @return int|null
     */
    private function findHighestIdMergedInBranch(string $content, string $branch): ?int
    {
        $allIdsInChangelog = $this->idsAnalyzer->getAllIdsInChangelog($content);
        rsort($allIdsInChangelog);
        foreach ($allIdsInChangelog as $id) {
            $idInt = (int) $id;
            if ($this->githubApi->isPullRequestMergedToBaseBranch($idInt, $branch)) {
                return $idInt;
            }
        }
        return null;
    }
}

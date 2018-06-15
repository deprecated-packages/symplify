<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Output;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class DumpMergesReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    public function __construct(SymfonyStyle $symfonyStyle, GitCommitDateTagResolver $gitCommitDateTagResolver)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->gitCommitDateTagResolver = $gitCommitDateTagResolver;
    }

    /**
     * @param Change[] $changes
     */
    public function reportChanges(array $changes, bool $withTags): void
    {
        if (! $withTags) {
            $this->symfonyStyle->newLine(1);
        } else {
            $changes = $this->sortChangesByTags($changes);
        }

        $previousTag = '';
        foreach ($changes as $change) {
            if ($withTags) {
                if ($previousTag !== $change->getTag()) {
                    $this->symfonyStyle->newLine(1);
                    $this->symfonyStyle->writeln('## ' . $this->createTagLine($change));
                    $this->symfonyStyle->newLine(1);
                }

                $previousTag = $change->getTag();
            }

            $this->symfonyStyle->writeln($change->getMessage());
        }

        $this->symfonyStyle->newLine(1);
    }

    /**
     * @param Change[] $changes
     */
    public function reportChangesWithHeadlines(
        array $changes,
        bool $withCategories,
        bool $withPackages,
        bool $withTags,
        string $priority
    ): void {
        // only categories
        if ($withCategories && ! $withPackages) {
            $this->reportChangesByCategories($changes);
            return;
        }

        // only packages
        if ($withPackages && ! $withCategories) {
            $this->reportChangesByPackages($changes);
            return;
        }

        $this->reportChangesByCategoriesAndPackages($changes, $priority);
    }

    /**
     * @param Change[] $changes
     */
    private function reportChangesByPackages(array $changes): void
    {
        $previousPackage = '';
        foreach ($changes as $change) {
            if ($previousPackage !== $change->getPackage()) {
                $this->symfonyStyle->newLine(1);
                $this->symfonyStyle->writeln('### ' . $change->getPackage());
                $this->symfonyStyle->newLine(1);
            }

            $this->symfonyStyle->writeln($change->getMessageWithoutPackage());

            $previousPackage = $change->getPackage();
        }

        $this->symfonyStyle->newLine(1);
        return;
    }

    /**
     * @param Change[] $changes
     */
    private function reportChangesByCategories(array $changes): void
    {
        $previousCategory = '';
        foreach ($changes as $change) {
            if ($previousCategory !== $change->getCategory()) {
                $this->symfonyStyle->newLine(1);
                $this->symfonyStyle->writeln('### ' . $change->getCategory());
                $this->symfonyStyle->newLine(1);
            }

            $this->symfonyStyle->writeln($change->getMessage());

            $previousCategory = $change->getCategory();
        }

        $this->symfonyStyle->newLine(1);
    }

    /**
     * @param Change[] $changes
     */
    private function reportChangesByCategoriesAndPackages(array $changes, string $priority): void
    {
        $previousPrimary = '';
        $previousSecondary = '';

        foreach ($changes as $change) {
            if ($priority === ChangeSorter::PRIORITY_PACKAGES) {
                $currentPrimary = $change->getPackage();
                $currentSecondary = $change->getCategory();
            } else {
                $currentPrimary = $change->getCategory();
                $currentSecondary = $change->getPackage();
            }

            $this->reportHeadline($previousPrimary, $currentPrimary, $previousSecondary, $currentSecondary);

            $this->symfonyStyle->writeln($change->getMessageWithoutPackage());

            $previousPrimary = $currentPrimary;
            $previousSecondary = $currentSecondary;
        }

        $this->symfonyStyle->newLine(1);
    }

    private function reportHeadline(
        string $previousPrimary,
        string $currentPrimary,
        string $previousSecondary,
        string $currentSecondary
    ): void {
        $spaceAlreadyAdded = false;

        if ($previousPrimary !== $currentPrimary) {
            $this->symfonyStyle->newLine(1);
            $this->symfonyStyle->writeln('### ' . $currentPrimary);
            $this->symfonyStyle->newLine(1);
            $spaceAlreadyAdded = true;

            $previousSecondary = null;
        }

        if ($previousSecondary !== $currentSecondary) {
            if (! $spaceAlreadyAdded) {
                $this->symfonyStyle->newLine(1);
            }

            $this->symfonyStyle->writeln('#### ' . $currentSecondary);
            $this->symfonyStyle->newLine(1);
        }
    }

    private function createTagLine(Change $change): string
    {
        $tagLine = $change->getTag();

        $tagDate = $this->gitCommitDateTagResolver->resolveDateForTag($change->getTag());
        if ($tagDate) {
            $tagLine .= ' - ' . $tagDate;
        }

        return $tagLine;
    }

    /**
     * @inspiration https://stackoverflow.com/questions/25475196/sort-array-that-specific-values-will-be-first
     *
     * @param Change[] $changes
     * @return Change[]
     */
    private function sortChangesByTags(array $changes): array
    {
        usort($changes, function (Change $firstChange, Change $secondChange) {
            // make "Unreleased" first
            if ($firstChange->getTag() === 'Unreleased') {
                return -1;
            }

            if ($secondChange->getTag() === 'Unreleased') {
                return 1;
            }

            // then sort by tags
            return version_compare($secondChange->getTag(), $firstChange->getTag());
        });

        return $changes;
    }
}

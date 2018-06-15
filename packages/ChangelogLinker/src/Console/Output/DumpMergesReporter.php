<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Output;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class DumpMergesReporter
{
    /**
     * @var string|null
     */
    private $previousCategory;

    /**
     * @var string|null
     */
    private $previousPackage;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    /**
     * @var bool
     */
    private $withTags = false;

    /**
     * @var string|null
     */
    private $previousTag = null;

    /**
     * @var bool
     */
    private $withCategories = false;

    /**
     * @var bool
     */
    private $withPackages = false;

    /**
     * @var bool
     */
    private $wasEmptyLineBefore = false;

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
        $this->withTags = $withTags;

        if (! $this->withTags) {
            $this->symfonyStyle->newLine(1);
        }

        foreach ($changes as $change) {
            if ($this->withTags) {
                if ($this->previousTag !== $change->getTag()) {
                    $this->symfonyStyle->newLine(1);
                    $this->symfonyStyle->writeln('## ' . $this->createTagLine($change));
                    $this->symfonyStyle->newLine(1);
                }

                $this->previousTag = $change->getTag();
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
        $this->withTags = $withTags;
        $this->withCategories = $withCategories;
        $this->withPackages = $withPackages;

        // only categories or only packages
        if (($this->withCategories && ! $this->withPackages) || ($this->withPackages && ! $this->withCategories)) {
            $this->reportChangesByOneGroup($changes);
            return;
        }

        $this->reportChangesByCategoriesAndPackages($changes, $priority);
    }

    /**
     * @param Change[] $changes
     */
    private function reportChangesByOneGroup(array $changes): void
    {
        foreach ($changes as $change) {
            if ($this->withTags) {
                if ($this->previousTag !== $change->getTag()) {
                    $this->symfonyStyle->newLine(1);
                    $this->symfonyStyle->writeln('## ' . $this->createTagLine($change));
                    $this->addEmptyLineIfNotYet();
                }

                $this->previousTag = $change->getTag();
            }

            if ($this->withPackages) {
                if ($this->previousPackage !== $change->getPackage()) {
                    $this->addEmptyLineIfNotYet();

                    $this->symfonyStyle->writeln('### ' . $change->getPackage());
                    $this->symfonyStyle->newLine(1);
                }

                $this->previousPackage = $change->getPackage();
            }

            if ($this->withCategories) {
                if ($this->previousCategory !== $change->getCategory()) {
                    $this->addEmptyLineIfNotYet();

                    $this->symfonyStyle->writeln('### ' . $change->getCategory());
                    $this->symfonyStyle->newLine(1);
                }

                $this->previousCategory = $change->getCategory();
            }

            $message = $this->withPackages ? $change->getMessageWithoutPackage() : $change->getMessage();
            $this->symfonyStyle->writeln($message);
        }

        $this->symfonyStyle->newLine(1);
        return;
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
        if ($previousPrimary !== $currentPrimary) {
            $this->symfonyStyle->newLine(1);
            $this->symfonyStyle->writeln('### ' . $currentPrimary);
            $this->addEmptyLineIfNotYet();

            $previousSecondary = null;
        }

        if ($previousSecondary !== $currentSecondary) {
            $this->addEmptyLineIfNotYet();

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

    private function addEmptyLineIfNotYet(): void
    {
        if ($this->wasEmptyLineBefore) {
            $this->wasEmptyLineBefore = false;
        } else {
            $this->symfonyStyle->newLine(1);
            $this->wasEmptyLineBefore = true;
        }
    }
}

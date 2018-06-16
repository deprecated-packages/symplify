<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Output;

use Nette\Utils\Strings;
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
     * @var string|null
     */
    private $previousTag;

    /**
     * @var bool
     */
    private $withTags = false;

    /**
     * @var bool
     */
    private $withCategories = false;

    /**
     * @var bool
     */
    private $withPackages = false;

    /**
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    /**
     * @var string
     */
    private $priority;

    /**
     * @var string
     */
    private $content;

    public function __construct(GitCommitDateTagResolver $gitCommitDateTagResolver)
    {
        $this->gitCommitDateTagResolver = $gitCommitDateTagResolver;
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
        $this->priority = $priority;

        $this->content .= PHP_EOL;

        // only categories or only packages
        if ($this->withCategories ^ $this->withPackages) {
            $this->reportChangesByOneGroup($changes);
            return;
        }

        $this->reportChangesByCategoriesAndPackages($changes);
    }

    /**
     * @param Change[] $changes
     */
    private function reportChangesByOneGroup(array $changes): void
    {
        foreach ($changes as $change) {
            $this->displayTagIfDesired($change);
            $this->displayPackageIfDesired($change);
            $this->displayCategoryIfDesired($change);

            $message = $this->withPackages ? $change->getMessageWithoutPackage() : $change->getMessage();
            $this->content .= $message . PHP_EOL;
        }

        $this->content .= PHP_EOL;
        return;
    }

    /**
     * @param Change[] $changes
     */
    private function reportChangesByCategoriesAndPackages(array $changes): void
    {
        $previousPrimary = '';
        $previousSecondary = '';

        foreach ($changes as $change) {
            $this->displayTagIfDesired($change);

            if ($this->priority === ChangeSorter::PRIORITY_PACKAGES) {
                $currentPrimary = $change->getPackage();
                $currentSecondary = $change->getCategory();
            } else {
                $currentPrimary = $change->getCategory();
                $currentSecondary = $change->getPackage();
            }

            if ($this->withPackages || $this->withPackages) {
                $this->reportHeadline($previousPrimary, $currentPrimary, $previousSecondary, $currentSecondary);
            }

            if ($this->withPackages) {
                $this->content .= $change->getMessageWithoutPackage() . PHP_EOL;
            } else {
                $this->content .= $change->getMessage() . PHP_EOL;
            }

            $previousPrimary = $currentPrimary;
            $previousSecondary = $currentSecondary;
        }

        $this->content .= PHP_EOL;
    }

    private function reportHeadline(
        string $previousPrimary,
        string $currentPrimary,
        string $previousSecondary,
        string $currentSecondary
    ): void {
        if ($previousPrimary !== $currentPrimary) {
            $this->content .= '### ' . $currentPrimary . PHP_EOL;
            $this->content .= PHP_EOL;

            $previousSecondary = null;
        }

        if ($previousSecondary !== $currentSecondary) {
            $this->content .= '#### ' . $currentSecondary . PHP_EOL;
            $this->content .= PHP_EOL;
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

    public function getContent(): string
    {
        // 2 lines from the start
        $this->content = Strings::replace($this->content, '#^(\n){2,}#', PHP_EOL);

        // 3 lines to 2
        return Strings::replace($this->content, '#(\n){3,}#', PHP_EOL . PHP_EOL);
    }

    private function hasTagChanged(Change $change): bool
    {
        $hasTagChanged = $this->previousTag !== $change->getTag();

        $this->previousTag = $change->getTag();

        return $hasTagChanged;
    }

    private function hasPackageChanged(Change $change): bool
    {
        $hasPackageChanged = $this->previousPackage !== $change->getPackage();

        $this->previousPackage = $change->getPackage();

        return $hasPackageChanged;
    }

    private function hasCategoryChanged(Change $change): bool
    {
        $hasCategoryChanged = $this->previousCategory !== $change->getCategory();

        $this->previousCategory = $change->getCategory();

        return $hasCategoryChanged;
    }

    private function displayCategoryIfDesired(Change $change): void
    {
        if ($this->withCategories && $this->hasCategoryChanged($change)) {
            $this->content .= $this->wrapByEmptyLines('### ' . $change->getCategory());
        }
    }

    private function displayPackageIfDesired(Change $change): void
    {
        if ($this->withPackages && $this->hasPackageChanged($change)) {
            $this->content .= $this->wrapByEmptyLines('### ' . $change->getPackage());
        }
    }

    private function displayTagIfDesired(Change $change): void
    {
        if ($this->withTags && $this->hasTagChanged($change)) {
            $this->content .= $this->wrapByEmptyLines('## ' . $this->createTagLine($change));
        }
    }

    private function wrapByEmptyLines(string $message): string
    {
        $content = PHP_EOL;
        $content .= $message . PHP_EOL;
        $content .= PHP_EOL;

        return $content;
    }
}

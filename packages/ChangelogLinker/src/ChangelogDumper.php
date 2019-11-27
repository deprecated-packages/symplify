<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;
use Symplify\PackageBuilder\Configuration\EolConfiguration;

final class ChangelogDumper
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
     * @var string
     */
    private $content;

    /**
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    /**
     * @var ChangelogFormatter
     */
    private $changelogFormatter;

    public function __construct(
        GitCommitDateTagResolver $gitCommitDateTagResolver,
        ChangelogFormatter $changelogFormatter
    ) {
        $this->gitCommitDateTagResolver = $gitCommitDateTagResolver;
        $this->changelogFormatter = $changelogFormatter;
    }

    /**
     * @param Change[] $changes
     */
    public function reportChangesWithHeadlines(
        array $changes,
        bool $withCategories,
        bool $withPackages,
        ?string $priority
    ): string {
        $eolChar = EolConfiguration::getEolChar();
        $this->content .= $eolChar;

        foreach ($changes as $change) {
            $this->displayHeadlines($withCategories, $withPackages, $priority, $change);

            $message = $withPackages ? $change->getMessageWithoutPackage() : $change->getMessage();
            $this->content .= $message . $eolChar;
        }

        $this->content .= $eolChar;

        return $this->changelogFormatter->format($this->content);
    }

    private function displayHeadlines(
        bool $withCategories,
        bool $withPackages,
        ?string $priority,
        Change $change
    ): void {
        $this->displayTag($change);

        if ($priority === ChangeSorter::PRIORITY_PACKAGES) {
            $this->displayPackageIfDesired($change, $withPackages, $priority);
            $this->displayCategoryIfDesired($change, $withCategories, $priority);
        } else {
            $this->displayCategoryIfDesired($change, $withCategories, $priority);
            $this->displayPackageIfDesired($change, $withPackages, $priority);
        }
    }

    private function displayTag(Change $change): void
    {
        if ($this->previousTag === $change->getTag()) {
            return;
        }

        $eolChar = EolConfiguration::getEolChar();
        $this->content .= '## ' . $this->createTagLine($change) . $eolChar;
        $this->previousTag = $change->getTag();
    }

    private function displayPackageIfDesired(Change $change, bool $withPackages, ?string $priority): void
    {
        if (! $withPackages || $this->previousPackage === $change->getPackage()) {
            return;
        }

        $eolChar = EolConfiguration::getEolChar();
        $headlineLevel = $priority === ChangeSorter::PRIORITY_CATEGORIES ? 4 : 3;
        $this->content .= str_repeat('#', $headlineLevel) . ' ' . $change->getPackage() . $eolChar;
        $this->previousPackage = $change->getPackage();
    }

    private function displayCategoryIfDesired(Change $change, bool $withCategories, ?string $priority): void
    {
        if (! $withCategories || $this->previousCategory === $change->getCategory()) {
            return;
        }

        $eolChar = EolConfiguration::getEolChar();
        $headlineLevel = $priority === ChangeSorter::PRIORITY_PACKAGES ? 4 : 3;
        $this->content .= str_repeat('#', $headlineLevel) . ' ' . $change->getCategory() . $eolChar;
        $this->previousCategory = $change->getCategory();
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
}

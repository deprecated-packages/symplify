<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Output;

use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\Console\Formatter\DumpMergesFormatter;
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

    /**
     * @var int
     */
    private $headlineLevel = 3;

    /**
     * @var DumpMergesFormatter
     */
    private $dumpMergesFormatter;

    public function __construct(
        GitCommitDateTagResolver $gitCommitDateTagResolver,
        DumpMergesFormatter $dumpMergesFormatter
    ) {
        $this->gitCommitDateTagResolver = $gitCommitDateTagResolver;
        $this->dumpMergesFormatter = $dumpMergesFormatter;
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

        foreach ($changes as $change) {
            $this->displayTagIfDesired($change);

            if ($this->priority === ChangeSorter::PRIORITY_PACKAGES) {
                $this->displayPackageIfDesired($change);
                if ($this->withCategories && $this->withPackages) {
                    $this->headlineLevel = 4;
                }
                $this->displayCategoryIfDesired($change);
            } else {
                $this->displayCategoryIfDesired($change);
                if ($this->withCategories && $this->withPackages) {
                    $this->headlineLevel = 4;
                }
                $this->displayPackageIfDesired($change);
            }

            $this->headlineLevel = 3;

            if ($this->withPackages) {
                $this->content .= $change->getMessageWithoutPackage() . PHP_EOL;
            } else {
                $this->content .= $change->getMessage() . PHP_EOL;
            }
        }

        $this->content .= PHP_EOL;
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
        return $this->dumpMergesFormatter->format($this->content);
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
            $this->content .= $this->wrapByEmptyLines(
                str_repeat('#', $this->headlineLevel) . ' ' . $change->getCategory()
            );
        }
    }

    private function displayPackageIfDesired(Change $change): void
    {
        if ($this->withPackages && $this->hasPackageChanged($change)) {
            $this->content .= $this->wrapByEmptyLines(
                str_repeat('#', $this->headlineLevel) . ' ' . $change->getPackage()
            );
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

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
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

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
        $this->content .= PHP_EOL;

        foreach ($changes as $change) {
            $this->displayTagIfDesired($change, $withTags);

            if ($priority === ChangeSorter::PRIORITY_PACKAGES) {
                $this->displayPackageIfDesired($change, $withPackages);
                if ($withCategories && $withPackages) {
                    $this->headlineLevel = 4;
                }
                $this->displayCategoryIfDesired($change, $withCategories);
            } else {
                $this->displayCategoryIfDesired($change, $withCategories);
                if ($withCategories && $withPackages) {
                    $this->headlineLevel = 4;
                }
                $this->displayPackageIfDesired($change, $withPackages);
            }

            $this->headlineLevel = 3;

            if ($withPackages) {
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

    private function displayTagIfDesired(Change $change, bool $withTags): void
    {
        if ($withTags === false) {
            return;
        }

        if ($this->previousTag === $change->getTag()) {
            return;
        }

        $this->content .= '## ' . $this->createTagLine($change) . PHP_EOL;
        $this->previousTag = $change->getTag();
    }

    private function displayCategoryIfDesired(Change $change, bool $withCategories): void
    {
        if ($withCategories === false) {
            return;
        }

        if ($this->previousCategory === $change->getCategory()) {
            return;
        }

        $this->content .= str_repeat('#', $this->headlineLevel) . ' ' . $change->getCategory() . PHP_EOL;

        $this->previousCategory = $change->getCategory();
    }

    private function displayPackageIfDesired(Change $change, bool $withPackages): void
    {
        if ($withPackages === false) {
            return;
        }

        if ($this->previousPackage === $change->getPackage()) {
            return;
        }

        $this->content .= str_repeat('#', $this->headlineLevel) . ' ' . $change->getPackage() . PHP_EOL;

        $this->previousPackage = $change->getPackage();
    }
}

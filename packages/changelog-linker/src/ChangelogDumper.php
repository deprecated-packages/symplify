<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;
use Symplify\ChangelogLinker\ValueObject\ChangelogFormat;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;

/**
 * @see \Symplify\ChangelogLinker\Tests\ChangelogDumper\ChangelogDumperTest
 */
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
    public function reportChangesWithHeadlines(array $changes, string $changelogFormat): string
    {
        $this->content .= PHP_EOL;

        foreach ($changes as $change) {
            $this->displayHeadlines($changelogFormat, $change);

            if (in_array(
                $changelogFormat,
                [
                    ChangelogFormat::PACKAGES_ONLY,
                    ChangelogFormat::PACKAGES_THEN_CATEGORIES,
                    ChangelogFormat::CATEGORIES_THEN_PACKAGES,
                ],
                true
            )) {
                $message = $change->getMessageWithoutPackage();
            } else {
                $message = $change->getMessage();
            }

            $this->content .= $message . PHP_EOL;
        }

        $this->content .= PHP_EOL;

        return $this->changelogFormatter->format($this->content);
    }

    private function displayHeadlines(string $changelogFormat, Change $change): void
    {
        $this->displayTag($change);

        if ($changelogFormat === ChangelogFormat::PACKAGES_THEN_CATEGORIES) {
            $this->displayPackageIfDesired($change, $changelogFormat);
            $this->displayCategoryIfDesired($change, $changelogFormat);
        } else {
            $this->displayCategoryIfDesired($change, $changelogFormat);
            $this->displayPackageIfDesired($change, $changelogFormat);
        }
    }

    private function displayTag(Change $change): void
    {
        if ($this->previousTag === $change->getTag()) {
            return;
        }

        $this->content .= '## ' . $this->createTagLine($change) . PHP_EOL;
        $this->previousTag = $change->getTag();
    }

    private function displayPackageIfDesired(Change $change, string $changelogFormat): void
    {
        if ($changelogFormat === ChangelogFormat::BARE) {
            return;
        }

        if ($changelogFormat === ChangelogFormat::CATEGORIES_ONLY) {
            return;
        }

        if ($this->previousPackage === $change->getPackage()) {
            return;
        }

        $headlineLevel = $changelogFormat === ChangelogFormat::CATEGORIES_THEN_PACKAGES ? 4 : 3;
        $this->content .= str_repeat('#', $headlineLevel) . ' ' . $change->getPackage() . PHP_EOL;
        $this->previousPackage = $change->getPackage();
    }

    private function displayCategoryIfDesired(Change $change, string $changelogFormat): void
    {
        if ($changelogFormat === ChangelogFormat::BARE) {
            return;
        }

        if ($changelogFormat === ChangelogFormat::PACKAGES_ONLY) {
            return;
        }

        if ($this->previousCategory === $change->getCategory()) {
            return;
        }

        $headlineLevel = $changelogFormat === ChangelogFormat::PACKAGES_THEN_CATEGORIES ? 4 : 3;
        $this->content .= str_repeat('#', $headlineLevel) . ' ' . $change->getCategory() . PHP_EOL;
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

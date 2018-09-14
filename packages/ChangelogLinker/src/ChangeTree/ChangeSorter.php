<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use function Safe\usort;

final class ChangeSorter
{
    /**
     * @var string
     */
    public const PRIORITY_PACKAGES = 'packages';

    /**
     * @var string
     */
    public const PRIORITY_CATEGORIES = 'categories';

    /**
     * Inspiration: https://stackoverflow.com/a/8598241/1348344
     *
     * Sorts packages, then category or vice versa, depends on 2nd parameter
     *
     * @param Change[] $changes
     * @return Change[]
     */
    public function sort(array $changes, ?string $priority): array
    {
        // pur newer versions to the top, and "Unreleased" first
        usort($changes, function (Change $firstChange, Change $secondChange) use ($priority) {
            $comparisonStatus = $this->compareTags($firstChange, $secondChange);
            if ($comparisonStatus !== 0) {
                return $comparisonStatus;
            }

            if ($priority === self::PRIORITY_PACKAGES) {
                return $this->comparePackagesOverCategories($firstChange, $secondChange);
            }

            return $this->compareCategoriesOverPackages($firstChange, $secondChange);
        });

        return $changes;
    }

    private function compareTags(Change $firstChange, Change $secondChange): int
    {
        // v9999 => put "Unreleased" first
        $firstTag = $firstChange->getTag() === 'Unreleased' ? 'v9999' : $firstChange->getTag();
        $secondTag = $secondChange->getTag() === 'Unreleased' ? 'v9999' : $secondChange->getTag();

        // -1 => put higher first
        return -1 * version_compare($firstTag, $secondTag);
    }

    private function comparePackagesOverCategories(Change $firstChange, Change $secondChange): int
    {
        $compareStatus = $firstChange->getPackage() <=> $secondChange->getPackage();
        if ($compareStatus !== 0) {
            return $compareStatus;
        }

        return $firstChange->getCategory() <=> $secondChange->getCategory();
    }

    private function compareCategoriesOverPackages(Change $firstChange, Change $secondChange): int
    {
        $compareStatus = $firstChange->getCategory() <=> $secondChange->getCategory();
        if ($compareStatus !== 0) {
            return $compareStatus;
        }

        return $firstChange->getPackage() <=> $secondChange->getPackage();
    }
}

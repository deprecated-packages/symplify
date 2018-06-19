<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

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
     * One method to rule them all
     *
     * @param Change[] $changes
     * @return Change[]
     */
    public function sort(array $changes, ?string $priority): array
    {
        $changes = $this->sortByCategoryAndPackage($changes, $priority);

        return $this->sortByTags($changes);
    }

    /**
     * Inspiration: https://stackoverflow.com/questions/3232965/sort-multidimensional-array-by-multiple-keys
     *
     * Sorts packages, then category or vice versa, depends on 2nd parameter
     *
     * @param Change[] $changes
     * @return Change[]
     */
    public function sortByCategoryAndPackage(array $changes, ?string $priority): array
    {
        $categoryList = array_map(function (Change $change) {
            return $change->getPackage();
        }, $changes);

        $packageList = array_map(function (Change $change) {
            return $change->getCategory();
        }, $changes);

        if ($priority === self::PRIORITY_PACKAGES) {
            $primaryList = $packageList;
            $secondaryList = $categoryList;
        } else {
            $primaryList = $categoryList;
            $secondaryList = $packageList;
        }

        array_multisort($secondaryList, $primaryList, $changes);

        return $changes;
    }

    /**
     * @inspiration https://stackoverflow.com/questions/25475196/sort-array-that-specific-values-will-be-first
     *
     * @param Change[] $changes
     * @return Change[]
     */
    public function sortByTags(array $changes): array
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

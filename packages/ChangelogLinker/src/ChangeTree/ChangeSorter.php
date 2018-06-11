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
     * Inspiration: https://stackoverflow.com/questions/3232965/sort-multidimensional-array-by-multiple-keys
     *
     * Sorts packages, then category or vice versa, depends on 2nd parameter
     *
     * @param Change[] $changes
     * @return Change[]
     */
    public function sortByCategoryAndPackage(array $changes, string $priority): array
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
}

<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

final class ChangeSorter
{
    /**
     * Inspiration: https://stackoverflow.com/questions/3232965/sort-multidimensional-array-by-multiple-keys
     *
     * Sorts packages, then category or vice versa, depends on 2nd parameter
     *
     * @param Change[] $changes
     * @return Change[]
     */
    public function sortByCategoryAndPackage(array $changes, bool $arePackagesFirst): array
    {
        $categoryList = array_map(function (Change $change) {
            return $change->getPackage();
        }, $changes);

        $packageList = array_map(function (Change $change) {
            return $change->getCategory();
        }, $changes);

        if ($arePackagesFirst) {
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

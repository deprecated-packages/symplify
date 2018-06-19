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
    public function sort(array $changes, ?string $priority): array
    {
        $tagList = $categoryList = array_map(function (Change $change) {
            return $change->getTag();
        }, $changes);

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

        // note: possible $tags conflict with version_compare(), v4.9 > v4.10 > v4.1
        // see https://stackoverflow.com/a/48974986/1348344
        array_multisort($tagList, $secondaryList, $primaryList, $changes);

        return $changes;
    }
}

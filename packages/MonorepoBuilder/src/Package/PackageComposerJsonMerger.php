<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Nette\Utils\Json;

final class PackageComposerJsonMerger
{
    /**
     * @param mixed[] $composerPackageFileInfos
     * @param string[] $sections
     * @return string[]
     */
    public function mergeFileInfos(array $composerPackageFileInfos, array $sections): array
    {
        $merged = [];

        foreach ($composerPackageFileInfos as $packageFile) {
            $packageComposerJson = Json::decode($packageFile->getContents(), Json::FORCE_ARRAY);

            foreach ($sections as $section) {
                if (! isset($packageComposerJson[$section])) {
                    continue;
                }

                $merged[$section] = array_merge($collected[$section] ?? [], $packageComposerJson[$section]);
            }
        }

        return $merged;
    }
}

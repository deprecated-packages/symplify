<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Nette\Utils\Json;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class PackageComposerJsonMerger
{
    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    public function __construct(ParametersMerger $parametersMerger)
    {
        $this->parametersMerger = $parametersMerger;
    }

    /**
     * @param SplFileInfo[] $composerPackageFileInfos
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

                $merged[$section] = $this->parametersMerger->merge(
                    $merged[$section] ?? [],
                    $packageComposerJson[$section]
                );
            }
        }

        return $merged;
    }
}

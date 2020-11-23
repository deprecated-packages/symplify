<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher;

use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPath;
use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPaths;

final class Psr4Filter
{
    /**
     * @param Psr4NamespaceToPath[] $psr4NamespacesToPaths
     * @return Psr4NamespaceToPaths[]
     */
    public function filter(array $psr4NamespacesToPaths): array
    {
        $pathsByNamespace = $this->groupByNamespaceAndFilterUnique($psr4NamespacesToPaths);

        $psr4NamespaceToPaths = [];
        foreach ($pathsByNamespace as $namespace => $psr4NamespacesToPaths) {
            $paths = [];
            foreach ($psr4NamespacesToPaths as $psr4NamespaceToPath) {
                $paths[] = $psr4NamespaceToPath->getPath();
            }

            $psr4NamespaceToPaths[] = new Psr4NamespaceToPaths($namespace, $paths);
        }

        return $psr4NamespaceToPaths;
    }

    /**
     * @param Psr4NamespaceToPath[] $psr4NamespacesToPaths
     * @return Psr4NamespaceToPath[][]
     */
    private function groupByNamespaceAndFilterUnique(array $psr4NamespacesToPaths): array
    {
        $groupedByNamespace = [];
        foreach ($psr4NamespacesToPaths as $psr4NamespaceToPath) {
            $groupedByNamespace[$psr4NamespaceToPath->getNamespace()][] = $psr4NamespaceToPath;
        }

        ksort($groupedByNamespace);
        foreach ($groupedByNamespace as $namespace => $psr4NamespacesToPaths) {
            $groupedByNamespace[$namespace] = array_unique($psr4NamespacesToPaths);
        }

        return $groupedByNamespace;
    }
}

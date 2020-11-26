<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\FileSystem;

use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPaths;

final class Psr4PathNormalizer
{
    /**
     * @var Psr4PathResolver
     */
    private $psr4PathResolver;

    public function __construct(Psr4PathResolver $psr4PathResolver)
    {
        $this->psr4PathResolver = $psr4PathResolver;
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespacesToPaths
     */
    public function normalizePsr4NamespaceToPathsToJsonsArray(array $psr4NamespacesToPaths): array
    {
        $data = [];

        foreach ($psr4NamespacesToPaths as $psr4NamespaceToPaths) {
            $namespaceRoot = $this->normalizeNamespaceRoot($psr4NamespaceToPaths->getNamespace());
            $data[$namespaceRoot] = $this->psr4PathResolver->resolvePaths($psr4NamespaceToPaths);
        }

        ksort($data);

        return $data;
    }

    private function normalizeNamespaceRoot(string $namespace): string
    {
        return rtrim($namespace, '\\') . '\\';
    }
}

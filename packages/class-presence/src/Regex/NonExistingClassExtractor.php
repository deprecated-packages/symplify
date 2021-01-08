<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Regex;

use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\ClassPresence\Tests\Regex\NonExistingClassExtractor\NonExistingClassExtractorTest
 */
final class NonExistingClassExtractor
{
    /**
     * @var ClassExtractor
     */
    private $classExtractor;

    public function __construct(ClassExtractor $classExtractor)
    {
        $this->classExtractor = $classExtractor;
    }

    /**
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $classes = $this->classExtractor->extractFromFileInfo($fileInfo);
        $nonExistingClasses = $this->filterNonExistingClasses($classes);
        if ($nonExistingClasses === []) {
            return [];
        }

        sort($nonExistingClasses);

        return $nonExistingClasses;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[][]
     */
    public function extractFromFileInfos(array $fileInfos): array
    {
        $nonExistingClassesByFile = [];
        foreach ($fileInfos as $fileInfo) {
            $nonExistingClasses = $this->extractFromFileInfo($fileInfo);
            if ($nonExistingClasses === []) {
                continue;
            }

            $nonExistingClassesByFile[$fileInfo->getRelativeFilePathFromCwd()] = $nonExistingClasses;
        }

        return $nonExistingClassesByFile;
    }

    /**
     * @param string[] $classes
     * @return string[]
     */
    private function filterNonExistingClasses(array $classes): array
    {
        return array_filter($classes, function (string $class): bool {
            return ! $this->doesClassExists($class);
        });
    }

    private function doesClassExists(string $className): bool
    {
        if (class_exists($className)) {
            return true;
        }

        if (interface_exists($className)) {
            return true;
        }

        return trait_exists($className);
    }
}

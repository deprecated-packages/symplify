<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ContentAnalyzer;

use Symplify\EasyCI\ValueObject\NonExistingClassesInFile;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\ContentAnalyzer\NonExistingClassExtractor\NonExistingClassExtractorTest
 */
final class NonExistingClassExtractor
{
    public function __construct(
        private ClassExtractor $classExtractor,
        private ClassLikeExistenceChecker $classLikeExistenceChecker
    ) {
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
     * @return NonExistingClassesInFile[]
     */
    public function extractFromFileInfos(array $fileInfos): array
    {
        $nonExistingClassesByFile = [];
        foreach ($fileInfos as $fileInfo) {
            $nonExistingClasses = $this->extractFromFileInfo($fileInfo);
            if ($nonExistingClasses === []) {
                continue;
            }

            $nonExistingClassesByFile[] = new NonExistingClassesInFile($nonExistingClasses, $fileInfo);
        }

        return $nonExistingClassesByFile;
    }

    /**
     * @param class-string[] $classes
     * @return class-string[]
     */
    private function filterNonExistingClasses(array $classes): array
    {
        return array_filter(
            $classes,
            fn (string $class): bool => ! $this->classLikeExistenceChecker->doesClassLikeExist($class)
        );
    }
}

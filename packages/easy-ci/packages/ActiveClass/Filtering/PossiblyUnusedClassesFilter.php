<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Filtering;

use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class PossiblyUnusedClassesFilter
{
    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedNames
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedNames): array
    {
        $possiblyUnusedFilesWithClasses = [];

        $typesToSkip = $this->parameterProvider->provideArrayParameter(Option::TYPES_TO_SKIP);

        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedNames, true)) {
                continue;
            }

            // is excluded interfaces?
            foreach ($typesToSkip as $typeToSkip) {
                if ($this->isClassSkipped($fileWithClass, $typeToSkip)) {
                    continue 2;
                }
            }

            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }

        return $possiblyUnusedFilesWithClasses;
    }

    private function isClassSkipped(FileWithClass $fileWithClass, string $typeToSkip): bool
    {
        if (! str_contains($typeToSkip, '*')) {
            return is_a($fileWithClass->getClassName(), $typeToSkip, true);
        }

        // try fnmatch
        return fnmatch($typeToSkip, $fileWithClass->getClassName(), FNM_NOESCAPE);
    }
}

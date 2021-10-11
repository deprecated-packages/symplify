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
                if (is_a($fileWithClass->getClassName(), $typeToSkip, true)) {
                    continue 2;
                }
            }

            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }

        return $possiblyUnusedFilesWithClasses;
    }
}

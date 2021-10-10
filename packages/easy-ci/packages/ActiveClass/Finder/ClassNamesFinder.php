<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Finder;

use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassNamesFinder
{
    public function __construct(
        private ClassNameResolver $classNameResolver,
    ) {
    }

    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolveClassNamesToCheck(array $phpFileInfos): array
    {
        $checkClassNames = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            $className = $this->classNameResolver->resolveFromFromFileInfo($phpFileInfo);
            if ($className === null) {
                continue;
            }

            $checkClassNames[] = $className;
        }

        return $checkClassNames;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Finder;

use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassNamesFinder
{
    public function __construct(
        private ClassNameResolver $classNameResolver,
    ) {
    }

    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $phpFileInfos): array
    {
        $filesWithClasses = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            $className = $this->classNameResolver->resolveFromFromFileInfo($phpFileInfo);
            if ($className === null) {
                continue;
            }

            $filesWithClasses[] = new FileWithClass($phpFileInfo, $className);
        }

        return $filesWithClasses;
    }
}

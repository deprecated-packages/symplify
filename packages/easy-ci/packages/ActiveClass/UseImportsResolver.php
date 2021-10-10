<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class UseImportsResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/G02Uhv/1
     */
    private const USE_IMPORT_REGEX = '#^use (?<used_class>.*?);$#ms';

    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolveFromFileInfos(array $phpFileInfos): array
    {
        $useImports = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            $matches = Strings::matchAll($phpFileInfo->getContents(), self::USE_IMPORT_REGEX);

            foreach ($matches as $match) {
                $useImports[] = $match['used_class'];
            }
        }

        $uniqueUseImports = array_unique($useImports);
        sort($uniqueUseImports);

        return $uniqueUseImports;
    }
}

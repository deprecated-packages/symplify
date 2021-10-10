<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use Nette\Utils\Strings;

final class UsedNeonServicesResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/0d5p1M/1
     */
    private const NEON_CLASS_EXPLICIT_REGEX = '#class: (?<class_name>.*?)$#ms';

    /**
     * @var string
     * @see https://regex101.com/r/r29RU8/1
     */
    private const NEON_CLASS_LIST_REGEX = '#- (?<class_name>.*?)$#ms';

    /**
     * @return string[]
     */
    public function resolveFormFileInfos(array $neonFileInfos): array
    {
        $usedServices = [];

        foreach ($neonFileInfos as $neonFileInfo) {
            $classMatches = Strings::matchAll($neonFileInfo->getContents(), self::NEON_CLASS_EXPLICIT_REGEX);
            foreach ($classMatches as $classMatch) {
                $usedServices[] = $classMatch['class_name'];
            }

            $bulledClassMatches = Strings::matchAll($neonFileInfo->getContents(), self::NEON_CLASS_LIST_REGEX);
            foreach ($bulledClassMatches as $bulletClassMatch) {
                $usedServices[] = $bulletClassMatch['class_name'];
            }
        }

        $usedServices = array_unique($usedServices);
        sort($usedServices);

        return $usedServices;
    }
}

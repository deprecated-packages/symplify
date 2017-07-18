<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Finder;

use SplFileInfo;

interface CustomSourceFinderInterface
{
    /**
     * @param string[]
     * @return SplFileInfo[]
     */
    public function find(array $source): array;
}

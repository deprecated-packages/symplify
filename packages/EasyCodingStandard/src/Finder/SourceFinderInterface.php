<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use SplFileInfo;

interface SourceFinderInterface
{
    /**
     * @param string[]
     * @return SplFileInfo[]
     */
    public function find(array $source): array;
}

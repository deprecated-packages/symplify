<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Finder;

use SplFileInfo;

interface SourceProviderInterface
{
    /**
     * @return SplFileInfo[]
     */
    public function provide(): array;
}

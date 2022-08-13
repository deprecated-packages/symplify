<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Finder;

use Symplify\ConfigTransformer\ValueObject\Configuration;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConfigFileFinder
{
    public function __construct(
        private SmartFinder $smartFinder
    ) {
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findFileInfos(Configuration $configuration): array
    {
        $suffixes = $configuration->getInputSuffixes();
        $suffixesRegex = '#\.' . implode('|', $suffixes) . '$#';

        return $this->smartFinder->find($configuration->getSources(), $suffixesRegex);
    }
}

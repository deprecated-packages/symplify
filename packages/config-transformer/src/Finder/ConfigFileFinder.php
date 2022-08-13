<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Finder;

use Symplify\ConfigTransformer\ValueObject\Configuration;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConfigFileFinder
{
    /**
     * @see https://regex101.com/r/jmxqCg/1
     * @var string
     */
    private const CONFIG_SUFFIXES_REGEX = '#\.(yml|yaml|xml)$#';

    public function __construct(
        private SmartFinder $smartFinder
    ) {
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findFileInfos(Configuration $configuration): array
    {
        return $this->smartFinder->find($configuration->getSources(), self::CONFIG_SUFFIXES_REGEX);
    }
}

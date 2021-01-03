<?php

declare(strict_types=1);

namespace Symplify\EasyTesting;

use Nette\Utils\Strings;
use Symplify\EasyTesting\ValueObject\Prefix;
use Symplify\EasyTesting\ValueObject\SplitLine;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyTesting\Tests\MissingSkipPrefixResolver\MissingSkipPrefixResolverTest
 */
final class MissplacedSkipPrefixResolver
{


    /**
     * @param SmartFileInfo[] $fixtureFileInfos
     * @return SmartFileInfo[]
     */
    public function resolve(array $fixtureFileInfos): array
    {
        $invalidFileInfos = [];

        foreach ($fixtureFileInfos as $fixtureFileInfo) {
            $fileContents = $fixtureFileInfo->getContents();
            if (Strings::match($fileContents, SplitLine::SPLIT_LINE_REGEX)) {
                if ($this->hasNameSkipStart($fixtureFileInfo)) {
                    $invalidFileInfos[] = $fixtureFileInfo;
                }

                continue;
            }

            if ($this->hasNameSkipStart($fixtureFileInfo)) {
                continue;
            }

            $invalidFileInfos[] = $fixtureFileInfo;
        }

        return $invalidFileInfos;
    }

    private function hasNameSkipStart(SmartFileInfo $fixtureFileInfo): bool
    {
        return (bool) Strings::match($fixtureFileInfo->getBasenameWithoutSuffix(), Prefix::SKIP_PREFIX_REGEX);
    }
}

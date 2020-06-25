<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\Fixture;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\EasyTesting\ValueObject\SplitLine;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixtureSplitter
{
    /**
     * @return string[]
     */
    public function splitFileInfoToInputAndExpected(SmartFileInfo $smartFileInfo): array
    {
        if (Strings::match($smartFileInfo->getContents(), SplitLine::SPLIT_LINE)) {
            // original → expected
            return Strings::split($smartFileInfo->getContents(), SplitLine::SPLIT_LINE);
        }

        // no changes
        return [$smartFileInfo->getContents(), $smartFileInfo->getContents()];
    }

    /**
     * @return SmartFileInfo[]
     */
    public function splitFileInfoToLocalInputAndExpectedFileInfos(SmartFileInfo $smartFileInfo, bool $autoloadTestFixture = false): array
    {
        [$originalContent, $expectedContent] = $this->splitFileInfoToInputAndExpected($smartFileInfo);

        $originalFileInfo = $this->createTemporaryFileInfo($smartFileInfo, 'original', $originalContent);
        $expectedFileInfo = $this->createTemporaryFileInfo($smartFileInfo, 'expected', $expectedContent);

        // some files needs to be autoload to enable reflection
        if ($autoloadTestFixture) {
            require_once $originalFileInfo->getRealPath();
        }

        return [$originalFileInfo, $expectedFileInfo];
    }

    private function createTemporaryPathWithPrefix(SmartFileInfo $smartFileInfo, string $prefix): string
    {
        $hash = Strings::substring(md5($smartFileInfo->getRealPath()), 0, 5);

        return sprintf(
            sys_get_temp_dir() . '/ecs_temp_tests/%s_%s_%s',
            $prefix,
            $hash,
            $smartFileInfo->getBasename('.inc')
        );
    }

    private function createTemporaryFileInfo(SmartFileInfo $smartFileInfo, string $prefix, string $fileContent): SmartFileInfo
    {
        $temporaryFilePath = $this->createTemporaryPathWithPrefix($smartFileInfo, $prefix);
        FileSystem::write($temporaryFilePath, $fileContent);

        return new SmartFileInfo($temporaryFilePath);
    }
}

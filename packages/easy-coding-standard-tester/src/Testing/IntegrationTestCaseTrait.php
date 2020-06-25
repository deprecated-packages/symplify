<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandardTester\Testing;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\EasyTesting\Fixture\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @inspiration https://github.com/rectorphp/rector/blob/7039ce87c666d787c1d744343d449170d1655355/src/Testing/PHPUnit/IntegrationRectorTestCaseTrait.php
 */
trait IntegrationTestCaseTrait
{
    /**
     * @return string[]
     */
    protected function splitContentToOriginalFileAndExpectedFile(SmartFileInfo $smartFileInfo): array
    {
        [$inputContent, $expectedContent] = StaticFixtureSplitter::splitFileInfoToInputAndExpected($smartFileInfo);

        $inputFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'input');
        $expectedFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'expected');

        FileSystem::write($inputFile, $inputContent);
        FileSystem::write($expectedFile, $expectedContent);

        return [$inputFile, $expectedFile];
    }

    private function createTemporaryPathWithPrefix(SmartFileInfo $smartFileInfo, string $prefix): string
    {
        $hash = Strings::substring(md5($smartFileInfo->getPathname()), 0, 5);
        return sprintf(
            sys_get_temp_dir() . '/ecs_temp_tests/%s_%s_%s',
            $prefix,
            $hash,
            $smartFileInfo->getBasename('.inc')
        );
    }
}

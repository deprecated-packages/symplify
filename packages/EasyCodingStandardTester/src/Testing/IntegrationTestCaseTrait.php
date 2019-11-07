<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandardTester\Testing;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @inspiration https://github.com/rectorphp/rector/blob/7039ce87c666d787c1d744343d449170d1655355/src/Testing/PHPUnit/IntegrationRectorTestCaseTrait.php
 */
trait IntegrationTestCaseTrait
{
    /**
     * @var string
     */
    private $splitLine = '#-----\n#';

    /**
     * @return string[]
     */
    protected function splitContentToOriginalFileAndExpectedFile(SmartFileInfo $smartFileInfo): array
    {
        if (Strings::match($smartFileInfo->getContents(), $this->splitLine)) {
            // original → expected
            [
             $originalContent, $expectedContent,
            ] = Strings::split($smartFileInfo->getContents(), $this->splitLine);
        } else {
            // no changes
            $originalContent = $smartFileInfo->getContents();
            $expectedContent = $originalContent;
        }
        $originalFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'original');
        $expectedFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'expected');
        FileSystem::write($originalFile, $originalContent);
        FileSystem::write($expectedFile, $expectedContent);

        return [$originalFile, $expectedFile];
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

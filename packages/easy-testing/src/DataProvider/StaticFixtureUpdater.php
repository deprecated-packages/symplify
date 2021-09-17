<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\DataProvider;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class StaticFixtureUpdater
{
    public static function updateFixtureContent(
        SmartFileInfo|string $originalFileInfo,
        string $changedContent,
        SmartFileInfo $fixtureFileInfo
    ): void {
        if (! getenv('UPDATE_TESTS') && ! getenv('UT')) {
            return;
        }

        $newOriginalContent = self::resolveNewFixtureContent($originalFileInfo, $changedContent);

        self::getSmartFileSystem()
            ->dumpFile($fixtureFileInfo->getRealPath(), $newOriginalContent);
    }

    public static function updateExpectedFixtureContent(
        string $newOriginalContent,
        SmartFileInfo $expectedFixtureFileInfo
    ): void {
        if (! getenv('UPDATE_TESTS') && ! getenv('UT')) {
            return;
        }

        self::getSmartFileSystem()
            ->dumpFile($expectedFixtureFileInfo->getRealPath(), $newOriginalContent);
    }

    private static function getSmartFileSystem(): SmartFileSystem
    {
        return new SmartFileSystem();
    }

    private static function resolveNewFixtureContent(
        SmartFileInfo|string $originalFileInfo,
        string $changedContent
    ): string {
        if ($originalFileInfo instanceof SmartFileInfo) {
            $originalContent = $originalFileInfo->getContents();
        } else {
            $originalContent = $originalFileInfo;
        }

        if ($originalContent === $changedContent) {
            return $originalContent;
        }

        return $originalContent . '-----' . PHP_EOL . $changedContent;
    }
}

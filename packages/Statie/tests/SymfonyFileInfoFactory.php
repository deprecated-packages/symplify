<?php declare(strict_types=1);

namespace Symplify\Statie\Tests;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Dump helper class for emulating result of Symfony Finder
 */
final class SymfonyFileInfoFactory
{
    public static function createFromFilePath(string $filePath): SplFileInfo
    {
        return new SplFileInfo($filePath, '', '');
    }
}

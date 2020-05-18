<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Utils;

use PharIo\Version\Version;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Utils\VersionUtilsTest
 */
final class VersionUtils
{
    /**
     * @var string
     */
    private $packageAliasFormat;

    public function __construct(string $packageAliasFormat)
    {
        $this->packageAliasFormat = $packageAliasFormat;
    }

    /**
     * @param Version|string $version
     */
    public function getNextAliasFormat($version): string
    {
        $version = $this->normalizeVersion($version);

        if ($version->hasPreReleaseSuffix()) {
            $minor = $version->getMinor()->getValue();
        } else {
            $minor = $version->getMinor()->getValue() + 1;
        }

        return str_replace(
            ['<major>', '<minor>'],
            [$version->getMajor()->getValue(), $minor],
            $this->packageAliasFormat
        );
    }

    /**
     * @param Version|string $version
     */
    public function getRequiredNextFormat($version): string
    {
        $version = $this->normalizeVersion($version);

        if ($version->hasPreReleaseSuffix()) {
            $minor = $version->getMinor()->getValue();
        } else {
            $minor = $version->getMinor()->getValue() + 1;
        }

        return '^' . $version->getMajor()->getValue() . '.' . $minor;
    }

    /**
     * @param Version|string $version
     */
    public function getRequiredFormat($version): string
    {
        $version = $this->normalizeVersion($version);

        $requireVersion = '^' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();

        $patchVersion = $version->getPatch()->getValue();
        if ($patchVersion > 0) {
            $requireVersion .= '.' . $patchVersion;
        }

        return $requireVersion;
    }

    /**
     * @param Version|string $version
     */
    private function normalizeVersion($version): Version
    {
        if (is_string($version)) {
            return new Version($version);
        }

        return $version;
    }
}

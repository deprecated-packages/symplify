<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Utils;

use PharIo\Version\Version;

final class Utils
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
        if (is_string($version)) {
            $version = new Version($version);
        }

        return str_replace(
            ['<major>', '<minor>'],
            [$version->getMajor()->getValue(), $version->getMinor()->getValue() + 1],
            $this->packageAliasFormat
        );
    }

    /**
     * @param Version|string $version
     */
    public function getRequiredNextFormat($version): string
    {
        if (is_string($version)) {
            $version = new Version($version);
        }

        return '^' . $version->getMajor()->getValue() . '.' . ($version->getMinor()->getValue() + 1);
    }

    /**
     * @param Version|string $version
     */
    public function getRequiredFormat($version): string
    {
        if (is_string($version)) {
            $version = new Version($version);
        }

        $requireVersion = '^' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();

        $patchVersion = $version->getPatch()->getValue();
        if ($patchVersion > 0) {
            $requireVersion .= '.' . $patchVersion;
        }

        return $requireVersion;
    }
}

<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Utils;

use Nette\Utils\Strings;
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
    public function getNextVersionDevAliasForVersion($version): Version
    {
        if (is_string($version)) {
            $version = new Version($version);
        }

        $nextDevVersion = str_replace(
            ['<major>', '<minor>'],
            [$version->getMajor()->getValue(), $version->getMinor()->getValue() + 1],
            $this->packageAliasFormat
        );

        return new Version($nextDevVersion);
    }

    /**
     * @param Version|string $version
     */
    public function getNextVersionForVersion($version): Version
    {
        if (is_string($version)) {
            $version = new Version($version);
        }

        $startsWithV = Strings::startsWith($version->getVersionString(), 'v');

        $nextVersion = ($startsWithV ? 'v' : '') . $version->getMajor()->getValue() . '.' . ($version->getMinor()->getValue() + 1);

        return new Version($nextVersion);
    }
}

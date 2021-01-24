<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Init\Composer;

use Jean85\PrettyVersions;
use Nette\Utils\Json as NetteJson;
use OutOfBoundsException;
use PharIo\Version\InvalidVersionException;
use PharIo\Version\Version;
use Symplify\SmartFileSystem\SmartFileSystem;

final class PackageNameVersionProvider
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * Returns current version of MonorepoBuilder, contains only major and minor.
     */
    public function provide(string $packageName): string
    {
        $version = null;

        try {
            $prettyVersion = PrettyVersions::getVersion($packageName)
                ->getPrettyVersion();

            $version = new Version(str_replace('x-dev', '0', $prettyVersion));
        } catch (OutOfBoundsException | InvalidVersionException $exceptoin) {
            // Version might not be explicitly set inside composer.json, looking for "vendor/composer/installed.json"
            $version = $this->extractFromComposer($packageName);
        }

        if ($version === null) {
            return 'Unknown';
        }

        return sprintf('^%d.%d', $version->getMajor()->getValue(), $version->getMinor()->getValue());
    }

    /**
     * Returns current version of MonorepoBuilder extracting it from "vendor/composer/installed.json".
     */
    private function extractFromComposer(string $packageName): ?Version
    {
        $vendorDirectory = dirname(__DIR__, 6);
        $installedJsonFilename = sprintf('%s/composer/installed.json', $vendorDirectory);

        if (is_file($installedJsonFilename)) {
            $installedJsonFileContent = $this->smartFileSystem->readFile($installedJsonFilename);
            $installedJson = NetteJson::decode($installedJsonFileContent);

            foreach ($installedJson as $installedPackage) {
                if ($installedPackage->name === $packageName) {
                    return new Version($installedPackage->version);
                }
            }
        }

        return null;
    }
}

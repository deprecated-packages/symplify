<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Init\Composer;

use Jean85\Exception\ReplacedPackageException;
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
            $prettyVersion = $this->getPrettyVersion($packageName, 'symplify/symplify');

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
     * Workaround for when the required package is executed in the monorepo or replaced in any other way
     *
     * @see https://github.com/symplify/symplify/pull/2901#issuecomment-771536136
     * @see https://github.com/Jean85/pretty-package-versions/pull/16#issuecomment-620550459
     */
    private function getPrettyVersion(string $packageName, string $replacingPackageName): string
    {
        try {
            return PrettyVersions::getVersion($packageName)
                ->getPrettyVersion();
        } catch (OutOfBoundsException | ReplacedPackageException $exception) {
            return PrettyVersions::getVersion($replacingPackageName)
                ->getPrettyVersion();
        }
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

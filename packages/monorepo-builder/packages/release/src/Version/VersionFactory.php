<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Version;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Guard\ReleaseGuard;
use Symplify\MonorepoBuilder\Release\ValueObject\StaticSemVersion;
use Symplify\MonorepoBuilder\Split\Git\GitManager;

final class VersionFactory
{
    /**
     * @var ReleaseGuard
     */
    private $releaseGuard;

    /**
     * @var GitManager
     */
    private $gitManager;

    public function __construct(ReleaseGuard $releaseGuard, GitManager $gitManager)
    {
        $this->releaseGuard = $releaseGuard;
        $this->gitManager = $gitManager;
    }

    public function createValidVersion(string $versionArgument, ?string $stage): Version
    {
        if (in_array($versionArgument, StaticSemVersion::getAll(), true)) {
            return $this->resolveNextVersionByVersionKind($versionArgument);
        }

        // this object performs validation of version
        $version = new Version($versionArgument);

        $this->releaseGuard->guardVersion($version, $stage);

        return $version;
    }

    private function resolveNextVersionByVersionKind(string $versionKind): Version
    {
        // get current version
        $mostRecentVersion = $this->gitManager->getMostRecentTag(getcwd());
        if ($mostRecentVersion === null) {
            // the very first tag
            return new Version('v0.1.0');
        }

        $mostRecentVersion = new Version($mostRecentVersion);

        $currentMajorVersion = $mostRecentVersion->getMajor()->getValue();
        $currentMinorVersion = $mostRecentVersion->getMinor()->getValue();
        $currentPatchVersion = $mostRecentVersion->getPatch()->getValue();

        if ($versionKind === StaticSemVersion::MAJOR) {
            ++$currentMajorVersion;
            $currentMinorVersion = 0;
            $currentPatchVersion = 0;
        }

        if ($versionKind === StaticSemVersion::MINOR) {
            ++$currentMinorVersion;
            $currentPatchVersion = 0;
        }

        if ($versionKind === StaticSemVersion::PATCH) {
            ++$currentPatchVersion;
        }

        return new Version(sprintf('v%d.%d.%d', $currentMajorVersion, $currentMinorVersion, $currentPatchVersion));
    }
}

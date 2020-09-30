<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Version;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Guard\ReleaseGuard;
use Symplify\MonorepoBuilder\Release\ValueObject\SemVersion;
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
        if (in_array($versionArgument, SemVersion::ALL, true)) {
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

        $value = $mostRecentVersion->getMajor()
            ->getValue();
        $currentMinorVersion = $mostRecentVersion->getMinor()
            ->getValue();
        $currentPatchVersion = $mostRecentVersion->getPatch()
            ->getValue();

        if ($versionKind === SemVersion::MAJOR) {
            ++$value;
            $currentMinorVersion = 0;
            $currentPatchVersion = 0;
        }

        if ($versionKind === SemVersion::MINOR) {
            ++$currentMinorVersion;
            $currentPatchVersion = 0;
        }

        if ($versionKind === SemVersion::PATCH) {
            ++$currentPatchVersion;
        }

        return new Version(sprintf('v%d.%d.%d', $value, $currentMinorVersion, $currentPatchVersion));
    }
}

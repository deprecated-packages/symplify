<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Utils;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\MonorepoBuilder\ValueObjectFactory\VersionFactory;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Utils\VersionUtilsTest
 */
final class VersionUtils
{
    /**
     * @var string
     */
    private $packageAliasFormat;

    public function __construct(
        ParameterProvider $parameterProvider,
        private VersionFactory $versionFactory
    ) {
        $this->packageAliasFormat = $parameterProvider->provideStringParameter(Option::PACKAGE_ALIAS_FORMAT);
    }

    public function getNextAliasFormat(Version | string $version): string
    {
        $version = $this->normalizeVersion($version);

        /** @var Version $minor */
        $minor = $this->getNextMinorNumber($version);

        return str_replace(
            ['<major>', '<minor>'],
            [$version->getMajor()->getValue(), $minor],
            $this->packageAliasFormat
        );
    }

    public function getRequiredNextFormat(Version | string $version): string
    {
        $version = $this->normalizeVersion($version);
        $minor = $this->getNextMinorNumber($version);

        return '^' . $version->getMajor()->getValue() . '.' . $minor;
    }

    public function getRequiredFormat(Version | string $version): string
    {
        $version = $this->normalizeVersion($version);

        $requireVersion = '^' . $version->getMajor()->getValue() . '.' . $version->getMinor()->getValue();

        $value = $version->getPatch()
            ->getValue();
        if ($value > 0) {
            $requireVersion .= '.' . $value;
        }

        return $requireVersion;
    }

    private function normalizeVersion(Version | string $version): Version
    {
        if (is_string($version)) {
            return $this->versionFactory->create($version);
        }

        return $version;
    }

    private function getNextMinorNumber(Version $version): int
    {
        if ($version->hasPreReleaseSuffix()) {
            return (int) $version->getMinor()
                ->getValue();
        }

        return $version->getMinor()
            ->getValue() + 1;
    }
}

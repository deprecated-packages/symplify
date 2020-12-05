<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Composer;

use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use DateTimeInterface;
use Nette\Utils\DateTime;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\EasyCI\ValueObject\PhpVersionList;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\EasyCI\Tests\Composer\SupportedPhpVersionResolverTest
 */
final class SupportedPhpVersionResolver
{
    /**
     * @var VersionParser
     */
    private $versionParser;

    /**
     * @var Semver
     */
    private $semver;
    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    public function __construct(
        VersionParser $versionParser,
        Semver $semver,
        ComposerJsonFactory $composerJsonFactory
    ) {
        $this->versionParser = $versionParser;
        $this->semver = $semver;
        $this->composerJsonFactory = $composerJsonFactory;
    }

    /**
     * @return string[]
     */
    public function resolveFromComposerJsonFilePath(string $composerJsonFilePath): array
    {
        $composerJson = $this->composerJsonFactory->createFromFilePath($composerJsonFilePath);

        $requirePhpVersion = $composerJson->getRequirePhpVersion();
        if ($requirePhpVersion === null) {
            $message = sprintf('PHP version was not found in "%s"', $composerJsonFilePath);
            throw new ShouldNotHappenException($message);
        }

        return $this->resolveFromConstraints($requirePhpVersion, DateTime::from('now'));
    }

    /**
     * @return string[]
     */
    public function resolveFromConstraints(string $phpVersionConstraints, DateTimeInterface $todayDateTime): array
    {
        // to validate version
        $this->versionParser->parseConstraints($phpVersionConstraints);

        $supportedPhpVersion = [];

        foreach (PhpVersionList::VERSIONS_BY_RELEASE_DATE as $releaseDate => $phpVersion) {
            if (! $this->semver->satisfies($phpVersion, $phpVersionConstraints)) {
                continue;
            }

            // is in the future?
            $relaseDateTime = DateTime::from($releaseDate);
            if ($relaseDateTime > $todayDateTime) {
                continue;
            }

            $supportedPhpVersion[] = $phpVersion;
        }

        return $supportedPhpVersion;
    }
}

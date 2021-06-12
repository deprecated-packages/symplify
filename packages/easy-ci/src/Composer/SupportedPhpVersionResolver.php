<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Composer;

use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use DateTimeInterface;
use Nette\Utils\DateTime;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\EasyCI\Exception\ShouldNotHappenException;
use Symplify\EasyCI\ValueObject\PhpVersionList;

/**
 * @see \Symplify\EasyCI\Tests\Composer\SupportedPhpVersionResolverTest
 */
final class SupportedPhpVersionResolver
{
    public function __construct(
        private VersionParser $versionParser,
        private Semver $semver,
        private ComposerJsonFactory $composerJsonFactory
    ) {
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

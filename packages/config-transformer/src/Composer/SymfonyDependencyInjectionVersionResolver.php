<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Composer;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;

final class SymfonyDependencyInjectionVersionResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/Pf1yZh/1
     */
    private const FIRST_VERSION_REGEX = '#(?<lowest_version>\d.\d)#';

    public function resolve(): ?float
    {
        $projectComposerJsonFilePath = getcwd() . '/composer.json';
        if (! file_exists($projectComposerJsonFilePath)) {
            // nothing to find
            return null;
        }

        $composerJsonFileContents = FileSystem::read($projectComposerJsonFilePath);
        $composerJson = Json::decode($composerJsonFileContents, Json::FORCE_ARRAY);

        $symfonyDependencyInjectionVersion = $composerJson['require']['symfony/symfony'] ?? $composerJson['require']['symfony/dependency-injection'] ?? null;

        if ($symfonyDependencyInjectionVersion === null) {
            return null;
        }

        $dummyVersionMatch = Strings::match($symfonyDependencyInjectionVersion, self::FIRST_VERSION_REGEX);
        if ($dummyVersionMatch === null) {
            return null;
        }

        return (float) $dummyVersionMatch['lowest_version'];
    }
}

<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\FileSystem;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;

final class DirectoryToRepositoryProvider
{
    /**
     * @see https://regex101.com/r/BCPylL/1
     * @var string
     */
    private const ASTERISK_REGEX = '#\*#';

    /**
     * @var array<string, string>
     */
    private $directoriesToRepositories = [];

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    public function __construct(ParameterProvider $parameterProvider, FileSystemGuard $fileSystemGuard)
    {
        $this->directoriesToRepositories = $parameterProvider->provideArrayParameter(
            Option::DIRECTORIES_TO_REPOSITORIES
        );

        $this->fileSystemGuard = $fileSystemGuard;
    }

    /**
     * @return array<string, string>
     */
    public function getDirectoriesToRepositories(): array
    {
        $resolvedDirectoriesToRepository = [];

        foreach ($this->directoriesToRepositories as $directory => $repository) {
            if (! Strings::contains($directory, '*')) {
                $this->ensureDirectoryExists($directory);

                $resolvedDirectoriesToRepository[$directory] = $repository;
                continue;
            }

            // fnmatch
            $patternWithoutAsterisk = (string) trim($directory, '*');

            foreach ((array) glob($directory) as $foundDirectory) {
                /** @var string $foundDirectory */
                $exclusiveName = (string) Strings::after($foundDirectory, $patternWithoutAsterisk);
                $targetRepository = Strings::replace($repository, self::ASTERISK_REGEX, $exclusiveName);

                $resolvedDirectoriesToRepository[$foundDirectory] = $targetRepository;
            }
        }

        return $resolvedDirectoriesToRepository;
    }

    private function ensureDirectoryExists(string $directory): void
    {
        $extractMessage = sprintf(
            'Check "%s" parameter in your config.',
            Option::class . '::DIRECTORIES_TO_REPOSITORIES'
        );
        $this->fileSystemGuard->ensureDirectoryExists($directory, $extractMessage);
    }
}

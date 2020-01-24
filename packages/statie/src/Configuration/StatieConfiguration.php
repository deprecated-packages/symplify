<?php

declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Exception\Configuration\MissingGithubRepositorySlugException;

final class StatieConfiguration
{
    /**
     * @var string
     */
    private const OPTION_GITHUB_REPOSITORY_SOURCE_DIRECTORY = 'github_repository_source_directory';

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var string|null
     */
    private $sourceDirectory;

    /**
     * @var bool
     */
    private $isDryRun = false;

    /**
     * @var mixed[]
     */
    private $options = [];

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    public function __construct(ParameterProvider $parameterProvider, FileSystemGuard $fileSystemGuard)
    {
        $this->options += $parameterProvider->provide();
        $this->fileSystemGuard = $fileSystemGuard;
    }

    public function setSourceDirectory(string $sourceDirectory): void
    {
        $sourceDirectory = rtrim($sourceDirectory, '/');
        $this->fileSystemGuard->ensureDirectoryExists($sourceDirectory);
        $this->sourceDirectory = (new SmartFileInfo($sourceDirectory))->getRealPath();
    }

    public function setOutputDirectory(string $outputDirectory): void
    {
        $this->outputDirectory = $outputDirectory;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }

    public function getSourceDirectory(): string
    {
        if ($this->sourceDirectory) {
            return $this->sourceDirectory;
        }

        return getcwd() . DIRECTORY_SEPARATOR . 'source';
    }

    public function getGithubRepositorySourceDirectory(): string
    {
        if (isset($this->options[self::OPTION_GITHUB_REPOSITORY_SOURCE_DIRECTORY])) {
            return $this->options[self::OPTION_GITHUB_REPOSITORY_SOURCE_DIRECTORY];
        }

        throw new MissingGithubRepositorySlugException(sprintf(
            'Settings of "%s" is required for "{{ post|github_edit_post_url }}" filter. '
            . 'Add it to "statie.yml" under "parameters" section, e.g.: "%s".',
            self::OPTION_GITHUB_REPOSITORY_SOURCE_DIRECTORY,
            'https://github.com/TomasVotruba/tomasvotruba.cz/tree/master/source'
        ));
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function addOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    /**
     * @return mixed|null
     */
    public function getOption(string $name)
    {
        return $this->options[$name] ?? null;
    }

    public function setDryRun(bool $isDryRun): void
    {
        $this->isDryRun = $isDryRun;
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }
}

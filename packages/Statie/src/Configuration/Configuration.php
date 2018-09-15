<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\Exception\Configuration\MissingGithubRepositorySlugException;
use Symplify\Statie\FileSystem\FileSystemGuard;
use function Safe\getcwd;
use function Safe\realpath;
use function Safe\sprintf;

final class Configuration
{
    /**
     * @var string
     */
    private const OPTION_GITHUB_REPOSITORY_SOURCE_DIRECTORY = 'github_repository_source_directory';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var bool
     */
    private $isDryRun = false;

    public function __construct(ParameterProvider $parameterProvider, FileSystemGuard $fileSystemGuard)
    {
        $this->options += $parameterProvider->provide();
        $this->fileSystemGuard = $fileSystemGuard;
    }

    public function setSourceDirectory(string $sourceDirectory): void
    {
        $sourceDirectory = rtrim($sourceDirectory, '/');
        $this->fileSystemGuard->ensureDirectoryExists($sourceDirectory);
        $this->sourceDirectory = realpath($sourceDirectory);
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
            'Settings of "%s" is required for "{$post|githubEditPostUrl}" Latte filter. '
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

    /**
     * @param mixed $value
     */
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

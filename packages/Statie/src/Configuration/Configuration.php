<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\Exception\Configuration\MissingGithubRepositorySlugException;
use Symplify\Statie\Utils\FilesystemChecker;

final class Configuration
{
    /**
     * @var string
     */
    public const OPTION_GITHUB_REPOSITORY_SLUG = 'github_repository_slug';

    /**
     * @var string
     */
    public const OPTION_MARKDOWN_HEADLINE_ANCHORS = 'markdown_headline_anchors';

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
     * @var FilesystemChecker
     */
    private $filesystemChecker;

    public function __construct(ParameterProvider $parameterProvider, FilesystemChecker $filesystemChecker)
    {
        $this->options += $parameterProvider->provide();
        $this->filesystemChecker = $filesystemChecker;
    }

    public function setSourceDirectory(string $sourceDirectory): void
    {
        $sourceDirectory = rtrim($sourceDirectory, '/');
        $this->filesystemChecker->ensureDirectoryExists($sourceDirectory);
        $this->sourceDirectory = $sourceDirectory;
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

    public function getGithubRepositorySlug(): string
    {
        if (isset($this->options[self::OPTION_GITHUB_REPOSITORY_SLUG])) {
            return $this->options[self::OPTION_GITHUB_REPOSITORY_SLUG];
        }

        throw new MissingGithubRepositorySlugException(sprintf(
            'Settings of "%s" is required for "{$post|githubEditPostUrl}" Latte filter. '
            . 'Add it to "statie.neon" under "parameters" section, e.g.: "%s".',
            self::OPTION_GITHUB_REPOSITORY_SLUG,
            'TomasVotruba/tomasvotruba.cz'
        ));
    }

    public function isMarkdownHeadlineAnchors(): bool
    {
        return $this->options[self::OPTION_MARKDOWN_HEADLINE_ANCHORS] ?? false;
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function enableMarkdownHeadlineAnchors(): void
    {
        $this->options[self::OPTION_MARKDOWN_HEADLINE_ANCHORS] = true;
    }

    public function disableMarkdownHeadlineAnchors(): void
    {
        $this->options[self::OPTION_MARKDOWN_HEADLINE_ANCHORS] = false;
    }

    /**
     * @param mixed $value
     */
    public function addOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }
}

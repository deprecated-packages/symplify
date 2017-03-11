<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use SplFileInfo;
use Symplify\Statie\Configuration\Parser\NeonParser;

final class Configuration
{
    /**
     * @var string
     */
    public const OPTION_POST_ROUTE = 'postRoute';

    /**
     * @var string
     */
    public const OPTION_GITHUB_REPOSITORY_SLUG = 'githubRepositorySlug';

    /**
     * @var string
     */
    public const OPTION_MARKDOWN_HEADLINE_ANCHORS = 'markdownHeadlineAnchors';

    /**
     * @var bool
     */
    private const DEFAULT_MARKDOWN_HEADLINE_ANCHORS = false;

    /**
     * @var string
     */
    private const DEFAULT_POST_ROUTE = 'blog/:year/:month/:day/:title';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var NeonParser
     */
    private $neonParser;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(NeonParser $neonParser)
    {
        $this->neonParser = $neonParser;
    }


    /**
     * @param SplFileInfo[] $files
     */
    public function loadFromFiles(array $files): void
    {
        foreach ($files as $file) {
            $options = $this->neonParser->decodeFromFile($file->getRealPath());
            $this->loadFromArray($options);

        }
    }

    /**
     * @param mixed[] $options
     */
    public function loadFromArray(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param string $name
     * @param string|string[] $value
     */
    public function addGlobalVarialbe(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    public function setSourceDirectory(string $sourceDirectory): void
    {
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

    public function getPostRoute(): string
    {
        return $this->options['configuration'][self::OPTION_POST_ROUTE]
            ?? self::DEFAULT_POST_ROUTE;
    }

    public function getGithubRepositorySlug(): string
    {
        return $this->options['configuration'][self::OPTION_GITHUB_REPOSITORY_SLUG]
            ?? self::DEFAULT_POST_ROUTE;
    }

    public function isMarkdownHeadlineAnchors(): bool
    {
        return $this->options['configuration'][self::OPTION_MARKDOWN_HEADLINE_ANCHORS]
            ?? self::DEFAULT_MARKDOWN_HEADLINE_ANCHORS;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}

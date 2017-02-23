<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use SplFileInfo;
use Symplify\Statie\Configuration\Parser\NeonParser;

final class Configuration
{
    /**
     * @var string
     */
    private const OPTION_POST_ROUTE = 'postRoute';

    /**
     * @var string
     */
    private const OPTION_GITHUB_REPOSITORY_SLUG = 'githubRepositorySlug';

    /**
     * @var string
     */
    private const OPTION_MARKDOWN_HEADLINE_ANCHORS = 'markdownHeadlineAnchors';

    /**
     * @var string
     */
    private const DEFAULT_POST_ROUTE = 'blog/:year/:month/:day/:title';

    /**
     * @var array
     */
    private $globalVariables = [];

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

    /**
     * @var string
     */
    private $postRoute = self::DEFAULT_POST_ROUTE;

    /**
     * @var string
     */
    private $githubRepositorySlug;

    /**
     * @var bool
     */
    private $markdownHeadlineAnchors = false;

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
            $decodedOptions = $this->neonParser->decodeFromFile($file->getRealPath());
            $decodedOptions = $this->extractPostRoute($decodedOptions);
            $decodedOptions = $this->extractGithubRepositorySlug($decodedOptions);
            $decodedOptions = $this->extractMarkdownHeadlineAnchors($decodedOptions);
            $this->globalVariables = array_merge($this->globalVariables, $decodedOptions);
        }
    }

    /**
     * @param string       $name
     * @param string|array $value
     */
    public function addGlobalVarialbe(string $name, $value)
    {
        $this->globalVariables[$name] = $value;
    }

    public function getGlobalVariables(): array
    {
        return $this->globalVariables;
    }

    public function setSourceDirectory(string $sourceDirectory): void
    {
        $this->sourceDirectory = $sourceDirectory;
    }

    public function getSourceDirectory(): string
    {
        if ($this->sourceDirectory) {
            return $this->sourceDirectory;
        }

        return getcwd() . DIRECTORY_SEPARATOR . 'source';
    }

    public function setOutputDirectory(string $outputDirectory): void
    {
        $this->outputDirectory = $outputDirectory;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }

    public function setPostRoute(string $postRoute): void
    {
        $this->postRoute = $postRoute;
    }

    public function getPostRoute(): string
    {
        return $this->postRoute;
    }

    public function setGithubRepositorySlug(string $githubRepositorySlug): void
    {
        $this->githubRepositorySlug = $githubRepositorySlug;
    }

    public function getGithubRepositorySlug(): string
    {
        return $this->githubRepositorySlug;
    }

    public function setMarkdownHeadlineAnchors(bool $markdownHeadlineAnchors): void
    {
        $this->markdownHeadlineAnchors = $markdownHeadlineAnchors;
    }

    public function isMarkdownHeadlineAnchors(): bool
    {
        return $this->markdownHeadlineAnchors;
    }

    private function extractPostRoute(array $options): array
    {
        if (! isset($options['configuration'][self::OPTION_POST_ROUTE])) {
            return $options;
        }

        $this->setPostRoute($options['configuration'][self::OPTION_POST_ROUTE]);
        unset($options['configuration'][self::OPTION_POST_ROUTE]);

        return $options;
    }

    private function extractGithubRepositorySlug(array $options): array
    {
        if (! isset($options['configuration'][self::OPTION_GITHUB_REPOSITORY_SLUG])) {
            return $options;
        }

        $this->setGithubRepositorySlug($options['configuration'][self::OPTION_GITHUB_REPOSITORY_SLUG]);
        unset($options['configuration'][self::OPTION_GITHUB_REPOSITORY_SLUG]);

        return $options;
    }

    private function extractMarkdownHeadlineAnchors(array $options): array
    {
        if (! isset($options['configuration'][self::OPTION_MARKDOWN_HEADLINE_ANCHORS])) {
            return $options;
        }

        $this->setMarkdownHeadlineAnchors($options['configuration'][self::OPTION_MARKDOWN_HEADLINE_ANCHORS]);
        unset($options['configuration'][self::OPTION_MARKDOWN_HEADLINE_ANCHORS]);

        return $options;
    }
}

<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Configuration;

final class Configuration
{
    /**
     * @inspiration markdown comment: https://gist.github.com/jonikarppinen/47dc8c1d7ab7e911f4c9#gistcomment-2109856
     * @var string
     */
    public const CHANGELOG_PLACEHOLDER_TO_WRITE = '<!-- changelog-linker -->';

    /**
     * @var array|string[]
     */
    private $authorsToIgnore = [];

    /**
     * @var string
     */
    private $repositoryUrl;

    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var array|string[]
     */
    private $nameToUrls = [];

    /**
     * @var string[]
     */
    private $packageAliases = [];

    /**
     * @param string[] $authorsToIgnore
     * @param string[] $nameToUrls
     * @param string[] $packageAliases
     */
    public function __construct(
        array $authorsToIgnore,
        string $repositoryUrl,
        string  $repositoryName,
        array $nameToUrls,
        array $packageAliases
    ) {
        $this->authorsToIgnore = $authorsToIgnore;
        $this->repositoryUrl = $repositoryUrl;
        $this->repositoryName = $repositoryName;
        $this->nameToUrls = $nameToUrls;
        $this->packageAliases = $packageAliases;
    }

    /**
     * @return string[]
     */
    public function getAuthorsToIgnore(): array
    {
        return $this->authorsToIgnore;
    }

    public function getRepositoryUrl(): string
    {
        return $this->repositoryUrl;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    /**
     * @return string[]
     */
    public function getNameToUrls(): array
    {
        return $this->nameToUrls;
    }

    /**
     * @return string[]
     */
    public function getPackageAliases(): array
    {
        return $this->packageAliases;
    }
}

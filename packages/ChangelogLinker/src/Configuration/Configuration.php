<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Configuration;

final class Configuration
{
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
     * @param string[] $authorsToIgnore
     * @param string[] $nameToUrls
     */
    public function __construct(
        array $authorsToIgnore,
        string $repositoryUrl,
        string  $repositoryName,
        array $nameToUrls
    ) {
        $this->authorsToIgnore = $authorsToIgnore;
        $this->repositoryUrl = $repositoryUrl;
        $this->repositoryName = $repositoryName;
        $this->nameToUrls = $nameToUrls;
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
}

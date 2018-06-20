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
        array $nameToUrls,
        array $packageAliases
    ) {
        $this->authorsToIgnore = $authorsToIgnore;
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

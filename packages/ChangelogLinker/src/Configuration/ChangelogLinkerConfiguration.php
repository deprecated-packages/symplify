<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Configuration;

final class ChangelogLinkerConfiguration
{
    /**
     * @var string
     */
    private $repositoryLink;

    public function getRepositoryLink(): string
    {
        return $this->repositoryLink;
    }

    public function setRepositoryLink(string $repositoryLink): void
    {
        $this->repositoryLink = $repositoryLink;
    }
}

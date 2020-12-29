<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Parameter;

use Symplify\MonorepoBuilder\Github\GithubRepositoryResolver;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Parameter\ParameterSupplierTest
 */
final class ParameterSupplier
{
    /**
     * @var GithubRepositoryResolver
     */
    private $githubRepositoryResolver;

    public function __construct(GithubRepositoryResolver $githubRepositoryResolver)
    {
        $this->githubRepositoryResolver = $githubRepositoryResolver;
    }

    /**
     * @var mixed[] $packageDirectoriesData
     * @return array<string, mixed[]>
     */
    public function fillPackageDirectoriesWithDefaultData(array $packageDirectoriesData)
    {
        $completePackageDirectoriesData = [];
        foreach ($packageDirectoriesData as $packageDirectory => $data) {
            // If the key is an int, then the package directory is the value
            if (is_int($packageDirectory)) {
                $packageDirectory = $data;
                $data = [];
            }
            if (! is_string($packageDirectory) || ! is_array($data)) {
                throw new ShouldNotHappenException();
            }
            // If the organization is not provided, add the default one
            if (! isset($data['organization'])) {
                $data['organization'] = $this->githubRepositoryResolver->resolveGitHubRepositoryNameFromRemote();
            }
            $completePackageDirectoriesData[$packageDirectory] = $data;
        }
        return $completePackageDirectoriesData;
    }
}

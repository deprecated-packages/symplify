<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Parameter;

use Symplify\MonorepoBuilder\Parameter\ParameterSupplier;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\MonorepoBuilder\Github\GithubRepositoryResolver;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ParameterSupplierTest extends AbstractKernelTestCase
{
    /**
     * @var ParameterSupplier
     */
    private $parameterSupplier;
    /**
     * @var GithubRepositoryResolver
     */
    private $githubRepositoryResolver;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->parameterSupplier = $this->getService(ParameterSupplier::class);
        $this->githubRepositoryResolver = $this->getService(GithubRepositoryResolver::class);
    }

    public function testPackageDirectoriesAreComplete(): void
    {
        $completeConfig = [
            'symplify/monorepo-builder' => [
                'organization' => 'symplify',
            ],
            'symplify/package-for-migrify' => [
                'organization' => 'migrify',
            ],
        ];
        $this->assertEquals(
            $completeConfig,
            $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($completeConfig)
        );
    }

    public function testAddDefaultsToPackageDirectories(): void
    {
        $repoOwner = $this->githubRepositoryResolver->resolveGitHubRepositoryOwnerFromRemote();
        $configBefore = [
            'symplify/monorepo-builder' => [],
            'rector/rector' => null,
            'symplify/package-builder' => [
                'organization' => 'symplify',
            ],
            'symplify/package-for-migrify' => [
                'branch' => 'main',
            ],
            'symplify/package-for-migrify' => [
                'branch' => 'main',
                'organization' => 'migrify',
            ],
        ];
        $configAfter = [
            'symplify/monorepo-builder' => [
                'organization' => $repoOwner,
            ],
            'rector/rector' => [
                'organization' => $repoOwner,
            ],
            'symplify/package-builder' => [
                'organization' => 'symplify',
            ],
            'symplify/package-for-migrify' => [
                'branch' => 'main',
                'organization' => $repoOwner,
            ],
            'symplify/package-for-migrify' => [
                'branch' => 'main',
                'organization' => 'migrify',
            ],
        ];
        $this->assertEquals(
            $configAfter,
            $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($configBefore)
        );
    }

    public function testPackageDirectoriesAsKeys(): void
    {
        $repoOwner = $this->githubRepositoryResolver->resolveGitHubRepositoryOwnerFromRemote();
        $configBefore = [
            'symplify/monorepo-builder',
            'rector/rector',
        ];
        $configAfter = [
            'symplify/monorepo-builder' => [
                'organization' => $repoOwner,
            ],
            'rector/rector' => [
                'organization' => $repoOwner,
            ],
        ];
        $this->assertEquals(
            $configAfter,
            $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($configBefore)
        );
    }

    public function testIncorrectConfig(): void
    {
        $this->expectException(ShouldNotHappenException::class);
        $config = [
            'symplify/monorepo-builder' => 'symplify',
        ];
        $this->assertEquals(
            $config,
            $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($config)
        );
    }
}

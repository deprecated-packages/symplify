<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Github;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\MonorepoBuilder\Github\GithubRepositoryResolver;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use Symplify\MonorepoBuilder\Exception\Git\InvalidGitRemoteException;

final class GithubRepositoryResolverTest extends AbstractKernelTestCase
{
    /**
     * @var GithubRepositoryResolver
     */
    private $githubRepositoryResolver;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->githubRepositoryResolver = $this->getService(GithubRepositoryResolver::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $remoteUrl, string $expectedName): void
    {
        $this->assertSame(
            $expectedName,
            $this->githubRepositoryResolver->resolveGitHubRepositoryName($remoteUrl)
        );
    }

    public function provideData(): Iterator
    {
        yield ['git@github.com:symplify/symplify.git', 'symplify'];
        yield ['https://github.com/symplify/symplify.git', 'symplify'];
        yield ['https://UserName@github.com/symplify/symplify.git', 'symplify'];
        yield [
            'https://UserName:PassWord@github.com:443/symplify/symplify.git',
            'symplify',
        ];
        yield ['git@github.com:space/low-orbit.git', 'space'];
    }

    /**
     * @dataProvider provideInvalidData()
     */
    public function testInvalid(string $remoteUrl): void
    {
        $this->expectException(ShouldNotHappenException::class);
        $this->githubRepositoryResolver->resolveGitHubRepositoryName($remoteUrl);
    }

    public function provideInvalidData(): Iterator
    {
        yield ['https://www.my-company.com/symplify/symplify.git'];
        yield ['https://gitlab.com/my-group/my-user/my-repo.git'];
        yield ['https://git/user/project.git'];
    }
}

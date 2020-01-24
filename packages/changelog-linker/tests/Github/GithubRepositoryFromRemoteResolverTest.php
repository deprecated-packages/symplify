<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Github;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Exception\Git\InvalidGitRemoteException;
use Symplify\ChangelogLinker\Github\GithubRepositoryFromRemoteResolver;

final class GithubRepositoryFromRemoteResolverTest extends TestCase
{
    /**
     * @var GithubRepositoryFromRemoteResolver
     */
    private $githubRepositoryFromRemoteResolver;

    protected function setUp(): void
    {
        $this->githubRepositoryFromRemoteResolver = new GithubRepositoryFromRemoteResolver();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $commitHash, string $expectedHttpsUrl): void
    {
        $this->assertSame($expectedHttpsUrl, $this->githubRepositoryFromRemoteResolver->resolveFromUrl($commitHash));
    }

    public function provideData(): Iterator
    {
        yield ['git@github.com:symplify/symplify.git', 'https://github.com/symplify/symplify'];
        yield ['https://github.com/symplify/symplify.git', 'https://github.com/symplify/symplify'];
        yield ['https://UserName@github.com/symplify/symplify.git', 'https://github.com/symplify/symplify'];
        yield [
            'https://UserName:PassWord@github.com:443/symplify/symplify.git',
            'https://github.com/symplify/symplify',
        ];
        yield ['https://www.my-company.com/symplify/symplify.git', 'https://www.my-company.com/symplify/symplify'];
        yield ['https://gitlab.com/my-group/my-user/my-repo.git', 'https://gitlab.com/my-group/my-user/my-repo'];
        yield ['https://git/user/project.git', 'https://git/user/project'];
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidGitRemoteException::class);
        $this->githubRepositoryFromRemoteResolver->resolveFromUrl('http://url.git');
    }
}

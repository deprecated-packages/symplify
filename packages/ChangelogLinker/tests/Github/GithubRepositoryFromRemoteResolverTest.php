<?php declare(strict_types=1);

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
        yield ['git@github.com:Symplify/Symplify.git', 'https://github.com/Symplify/Symplify'];
        yield ['https://github.com/Symplify/Symplify.git', 'https://github.com/Symplify/Symplify'];
        yield ['https://UserName@github.com/Symplify/Symplify.git', 'https://github.com/Symplify/Symplify'];
        yield [
            'https://UserName:PassWord@github.com:443/Symplify/Symplify.git',
            'https://github.com/Symplify/Symplify',
        ];
        yield ['https://www.my-company.com/Symplify/Symplify.git', 'https://www.my-company.com/Symplify/Symplify'];
        yield ['https://gitlab.com/my-group/my-user/my-repo.git', 'https://gitlab.com/my-group/my-user/my-repo'];
        yield ['https://git/user/project.git', 'https://git/user/project'];
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidGitRemoteException::class);
        $this->githubRepositoryFromRemoteResolver->resolveFromUrl('http://url.git');
    }
}

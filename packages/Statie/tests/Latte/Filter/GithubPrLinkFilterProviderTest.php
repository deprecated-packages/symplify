<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Latte\Filter;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Latte\Filter\GithubPrLinkFilterProvider;
use Symplify\Statie\Latte\Filter\TimeFilterProvider;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GithubPrLinkFilterProviderTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/GithubPrLinkFilterProviderSource/statie-config-with-github-slug.neon'
        );
    }

    public function test(): void
    {
        /** @var TimeFilterProvider $githubPrLinkFilterProvider */
        $githubPrLinkFilterProvider = $this->container->get(GithubPrLinkFilterProvider::class);
        $githubEditPostUrlFilter = $githubPrLinkFilterProvider->provide()['githubEditPostUrl'];

        $abstractFileMock = $this->createMock(AbstractFile::class);
        $abstractFileMock->method('getRelativeSource')
            ->willReturn('_post/2017/2017-12-31-happy-new-years.md');

        $this->assertSame(
            'https://github.com/Organization/Repository/edit/master/source/_post/2017/2017-12-31-happy-new-years.md',
            $githubEditPostUrlFilter($abstractFileMock)
        );
    }
}

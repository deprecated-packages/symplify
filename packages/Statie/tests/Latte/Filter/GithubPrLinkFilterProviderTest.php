<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Latte\Filter;

use Symplify\Statie\Latte\Filter\GithubPrLinkFilterProvider;
use Symplify\Statie\Latte\Filter\TimeFilterProvider;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class GithubPrLinkFilterProviderTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var TimeFilterProvider $githubPrLinkFilterProvider */
        $githubPrLinkFilterProvider = $this->container->get(GithubPrLinkFilterProvider::class);
        $githubEditPostUrlFilter = $githubPrLinkFilterProvider->provide()['githubEditPostUrl'];

        $abstractFileMock = $this->createMock(AbstractFile::class);
        $abstractFileMock->method('getRelativeSource')
            ->willReturn('_post/2017/2017-12-31-happy-new-years.md');

        $this->assertSame(
            'https://github.com//edit/master/source/_post/2017/2017-12-31-happy-new-years.md',
            $githubEditPostUrlFilter($abstractFileMock)
        );
    }
}

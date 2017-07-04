<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Metrics;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Metrics\SimilarPostsResolver;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tests\Helper\PostFactory;

final class SimilarPostsResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PostFile
     */
    private $mainPost;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var SimilarPostsResolver
     */
    private $similarPostsResolver;

    protected function setUp(): void
    {
        $this->postFactory = new PostFactory;

        $this->similarPostsResolver = $this->container->get(SimilarPostsResolver::class);

        $configuration = $this->container->get(Configuration::class);
        $configuration->addPosts($this->getAllPosts());

        $this->mainPost = $this->postFactory->createPostFromFilePath(
            __DIR__ . '/../PostsSource/2017-01-05-another-related-post.md'
        );
    }

    public function testOrder(): void
    {
        $similarPosts = $this->similarPostsResolver->resolveForPostWithLimit($this->mainPost, 3);

        $mostSimilarPost = $similarPosts[0];
        $this->assertSame('Statie 4: How to Create The Simplest Blog', $mostSimilarPost['title']);
        $this->assertNotSame($this->mainPost['title'], $mostSimilarPost['title']);
    }

    public function testLimit(): void
    {
        $this->assertCount(3, $this->similarPostsResolver->resolveForPostWithLimit($this->mainPost, 3));
        $this->assertCount(1, $this->similarPostsResolver->resolveForPostWithLimit($this->mainPost, 1));
    }

    /**
     * @return PostFile[]
     */
    private function getAllPosts(): array
    {
        return [
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../PostsSource/2017-01-01-some-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../PostsSource/2017-01-05-some-related-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../PostsSource/2017-01-05-another-related-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../PostsSource/2017-02-05-offtopic-post.md'
            ),
        ];
    }
}

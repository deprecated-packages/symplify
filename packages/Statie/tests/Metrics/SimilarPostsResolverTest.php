<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Metrics;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Metrics\PostSimilarityAnalyzer;
use Symplify\Statie\Metrics\SimilarPostsResolver;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Tests\Helper\PostFactory;

final class SimilarPostsResolverTest extends TestCase
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

        $this->similarPostsResolver = $this->createSimilarPostsResolver();

        $this->mainPost = $this->postFactory->createPostFromFilePath(
            __DIR__ . '/../PostsSource/2017-01-01-some-post.md'
        );
    }

    public function testOrder(): void
    {
        $similarPosts = $this->similarPostsResolver->resolveForPostWithLimit($this->mainPost, 3);

        $mostSimilarPost = $similarPosts[0];
        $this->assertSame($this->mainPost['title'], $mostSimilarPost['title']);

        $this->assertNotSame($this->mainPost->getBaseName(), $mostSimilarPost->getBaseName());
    }

    public function testLimit(): void
    {
        $this->assertCount(2, $this->similarPostsResolver->resolveForPostWithLimit($this->mainPost, 2));
        $this->assertCount(1, $this->similarPostsResolver->resolveForPostWithLimit($this->mainPost, 1));
    }

    private function createSimilarPostsResolver(): SimilarPostsResolver
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->addGlobalVarialbe('posts', $this->getAllPosts());

        return new SimilarPostsResolver($configuration, new PostSimilarityAnalyzer);
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
            )
        ];
    }
}

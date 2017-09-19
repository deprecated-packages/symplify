<?php declare(strict_types=1);

namespace Symplify\Statie\SimilarPosts\Tests;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\SimilarPosts\SimilarPostsResolver;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tests\Helper\PostFactory;

final class SimilarPostsResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const POST_SOURCE_DIRECTORY = __DIR__ . '/../../../tests/PostsSource';

    /**
     * @var PostFile
     */
    private $mainPostFile;

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

        $this->mainPostFile = $this->postFactory->createPostFromFilePath(
            self::POST_SOURCE_DIRECTORY . '/2017-01-01-some-post.md'
        );
    }

    public function test(): void
    {
        $similarPosts = $this->similarPostsResolver->resolveForPost($this->mainPostFile);

        $this->assertCount(3, $similarPosts);

        $mostSimilarPost = $similarPosts[0];
        $this->assertSame('Statie 4: How to Create The Simplest Blog', $mostSimilarPost['title']);
        $this->assertNotSame($this->mainPostFile['title'], $mostSimilarPost['title']);
    }

    /**
     * @return PostFile[]
     */
    private function getAllPosts(): array
    {
        return [
            $this->postFactory->createPostFromFilePath(
                self::POST_SOURCE_DIRECTORY . '/2017-01-05-some-related-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                self::POST_SOURCE_DIRECTORY . '/2017-01-05-another-related-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                self::POST_SOURCE_DIRECTORY . '/2017-02-05-offtopic-post.md'
            ),
        ];
    }
}

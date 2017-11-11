<?php declare(strict_types=1);

namespace Symplify\Statie\RelatedPosts\Tests;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\RelatedPosts\RelatedPostsResolver;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tests\Helper\PostFactory;

final class RelatedPostsResolverTest extends AbstractContainerAwareTestCase
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
     * @var RelatedPostsResolver
     */
    private $relatedPostsResolver;

    protected function setUp(): void
    {
        $this->postFactory = new PostFactory();

        $this->relatedPostsResolver = $this->container->get(RelatedPostsResolver::class);

        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->addPosts($this->getAllPosts());

        $this->mainPostFile = $this->postFactory->createPostFromFilePath(
            self::POST_SOURCE_DIRECTORY . '/2017-01-01-some-post.md'
        );
    }

    public function test(): void
    {
        $relatedPosts = $this->relatedPostsResolver->resolveForPost($this->mainPostFile);

        $this->assertCount(3, $relatedPosts);

        $relatedPost = $relatedPosts[0];
        $this->assertSame('Statie 4: How to Create The Simplest Blog', $relatedPost['title']);
        $this->assertNotSame($this->mainPostFile['title'], $relatedPost['title']);
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

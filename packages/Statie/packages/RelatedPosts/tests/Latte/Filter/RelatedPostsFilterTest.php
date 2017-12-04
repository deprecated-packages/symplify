<?php declare(strict_types=1);

namespace Symplify\Statie\RelatedPosts\Tests\Latte\Filter;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\RelatedPosts\Latte\Filter\RelatedPostsFilter;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tests\Helper\PostFactory;

/**
 * @todo rename to general "related_items:"
 */
final class RelatedPostsFilterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const POST_SOURCE_DIRECTORY = __DIR__ . '/../../../../../tests/PostsSource';

    /**
     * @var RelatedPostsFilter
     */
    private $relatedPostsFilter;

    /**
     * @var PostFactory
     */
    private $postFactory;

    protected function setUp(): void
    {
        $this->postFactory = new PostFactory();
        $this->relatedPostsFilter = $this->container->get(RelatedPostsFilter::class);

        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->addOption('posts', $this->getAllPosts());
    }

    public function test(): void
    {
        $filters = $this->relatedPostsFilter->provide();

        $mainPost = $this->postFactory->createPostFromFilePath(
            self::POST_SOURCE_DIRECTORY . '/2017-01-01-some-post.md'
        );

        $relatedPosts = $filters['relatedPosts']($mainPost);

        $this->assertCount(3, $relatedPosts);
    }

    /**
     * @return PostFile[]
     */
    private function getAllPosts(): array
    {
        return [
            $this->postFactory->createPostFromFilePath(
                self::POST_SOURCE_DIRECTORY . '/2017-01-01-some-post.md'
            ),
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

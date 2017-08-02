<?php declare(strict_types=1);

namespace Symplify\Statie\SimilarPosts\Tests\Latte\Filter;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\SimilarPosts\Latte\Filter\SimilarPostsFilter;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tests\Helper\PostFactory;

final class SimilarPostsFilterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const POST_SOURCE_DIRECTORY = __DIR__ . '/../../../../../tests/PostsSource';

    /**
     * @var SimilarPostsFilter
     */
    private $similarPostFilter;

    /**
     * @var PostFactory
     */
    private $postFactory;

    protected function setUp(): void
    {
        $this->postFactory = new PostFactory;
        $this->similarPostFilter = $this->container->get(SimilarPostsFilter::class);

        $configuration = $this->container->get(Configuration::class);
        $configuration->addPosts($this->getAllPosts());
    }

    public function test(): void
    {
        $filters = $this->similarPostFilter->provide();

        $mainPost = $this->postFactory->createPostFromFilePath(
            self::POST_SOURCE_DIRECTORY . '/2017-01-01-some-post.md'
        );

        $similarPosts = $filters['similarPosts']($mainPost);

        $this->assertCount(2, $similarPosts);
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

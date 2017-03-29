<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Latte\Filter;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Latte\Filter\SimilarPostsFilter;
use Symplify\Statie\Metrics\PostSimilarityAnalyzer;
use Symplify\Statie\Metrics\SimilarPostsResolver;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Tests\Helper\PostFactory;
use TextAnalysis\Comparisons\CosineSimilarityComparison;

final class SimilarPostsFilterTest extends TestCase
{
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

        $similarPostResolver = $this->createSimilarPostResolver();
        $this->similarPostFilter = new SimilarPostsFilter($similarPostResolver);
    }

    public function test(): void
    {
        $filters = $this->similarPostFilter->getFilters();

        $mainPost = $this->postFactory->createPostFromFilePath(
            __DIR__ . '/../../PostsSource/2017-01-01-some-post.md'
        );

        $similarPosts = $filters['similarPosts']($mainPost, 3);

        $this->assertCount(3, $similarPosts);
    }

    /**
     * @return PostFile[]
     */
    private function getAllPosts(): array
    {
        return [
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../../PostsSource/2017-01-01-some-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../../PostsSource/2017-01-05-some-related-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../../PostsSource/2017-01-05-another-related-post.md'
            ),
            $this->postFactory->createPostFromFilePath(
                __DIR__ . '/../../PostsSource/2017-02-05-offtopic-post.md'
            )
        ];
    }

    private function createSimilarPostResolver(): SimilarPostsResolver
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->addGlobalVarialbe('posts', $this->getAllPosts());

        return new SimilarPostsResolver($configuration, new PostSimilarityAnalyzer(new CosineSimilarityComparison));
    }
}

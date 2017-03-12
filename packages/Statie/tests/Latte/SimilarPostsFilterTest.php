<?php declare(strict_types = 1);

namespace Symplify\Statie\Tests\Latte\Filter;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Latte\Filter\SimilarPostsFilter;
use Symplify\Statie\Renderable\Configuration\ConfigurationDecorator;
use Symplify\Statie\Renderable\File\PostFile;

final class SimilarPostsFilterTest extends TestCase
{
    /**
     * @var SimilarPostsFilter
     */
    private $similarPostFilter;

    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    protected function setUp()
    {
        $this->configurationDecorator = new ConfigurationDecorator(new NeonParser);

        $configuration = new Configuration(new NeonParser);
        $configuration->addGlobalVarialbe('posts', $this->getAllPosts());

        $this->similarPostFilter = new SimilarPostsFilter($configuration);
    }

    public function test()
    {
        $filters = $this->similarPostFilter->getFilters();

        $mainPost = $this->createPost(__DIR__ . '/SimilarPostFilterSource/2017-01-01-some-post.md');

        $similarPosts = $filters['similarPosts']($mainPost, 3);

        $this->assertCount(3, $similarPosts);
    }

    /**
     * @return PostFile[]
     */
    private function getAllPosts(): array
    {
        $posts[] = $this->createPost(__DIR__ . '/SimilarPostFilterSource/2017-01-01-some-post.md');
        $posts[] = $this->createPost(__DIR__ . '/SimilarPostFilterSource/2017-01-05-some-related-post.md');
        $posts[] = $this->createPost(__DIR__ . '/SimilarPostFilterSource/2017-01-05-another-related-post.md');
        $posts[] = $this->createPost(__DIR__ . '/SimilarPostFilterSource/2017-02-05-offtopic-post.md');

        return $posts;
    }

    private function createPost(string $filePath): PostFile
    {
        $fileInfo = new SplFileInfo($filePath);
        $post = new PostFile($fileInfo, $filePath);

        $this->configurationDecorator->decorateFile($post);

        return $post;
    }
}
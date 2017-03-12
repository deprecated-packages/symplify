<?php declare(strict_types = 1);

namespace Symplify\Statie\Tests\Latte\Filter;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Latte\Filter\SimilarPostsFilter;
use Symplify\Statie\Renderable\File\PostFile;

final class SimilarPostsFilterTest extends TestCase
{
    /**
     * @var SimilarPostsFilter
     */
    private $similarPostFilter;

    protected function setUp()
    {
        $configuration = new Configuration(new NeonParser);
        $this->similarPostFilter = new SimilarPostsFilter($configuration);
    }

    public function test()
    {
        $filters = $this->similarPostFilter->getFilters();

        $mainPost = $this->createPost(__DIR__ . '/SimilarPostFilterSource/2017-01-01-some-post.md');

        $similarPosts = $filters['similarPosts']($mainPost, 3);
        $this->assertCount(0, $similarPosts);
    }

    private function createPost(string $filePath): PostFile
    {
        $fileInfo = new SplFileInfo($filePath);

        return new PostFile($fileInfo, $filePath);
    }

    // tset here!!!

/// http://php.net/manual/en/function.similar-text.php

}
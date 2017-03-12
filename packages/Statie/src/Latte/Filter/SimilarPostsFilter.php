<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Filter;

use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;
use Symplify\Statie\Metrics\SimilarPostsResolver;
use Symplify\Statie\Renderable\File\PostFile;

final class SimilarPostsFilter implements LatteFiltersProviderInterface
{
    /**
     * @var string
     */
    private const FILTER_NAME = 'similarPosts';

    /**
     * @var SimilarPostsResolver
     */
    private $similarPostsResolver;

    public function __construct(SimilarPostsResolver $similarPostsResolver)
    {
        $this->similarPostsResolver = $similarPostsResolver;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            self::FILTER_NAME => function (PostFile $post, int $postCount) {
                return $this->similarPostsResolver->resolveForPostWithLimit($post, $postCount);
            }
        ];
    }
}

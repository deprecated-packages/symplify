<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Filter;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Metrics\SimilarPostsResolver;
use Symplify\Statie\Renderable\File\PostFile;

final class SimilarPostsFilter implements FilterProviderInterface
{
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
    public function provide(): array
    {
        return [
            // @todo usage
            'similarPosts' => function (PostFile $post, int $postCount) {
                return $this->similarPostsResolver->resolveForPostWithLimit($post, $postCount);
            }
        ];
    }
}

<?php declare(strict_types=1);

namespace Symplify\Statie\SimilarPosts\Latte\Filter;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\SimilarPosts\SimilarPostsResolver;

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
            // use in *.latte like this: {$post|similarPosts: 3}
            'similarPosts' => function (PostFile $post, int $postCount) {
                return $this->similarPostsResolver->resolveForPostWithLimit($post, $postCount);
            },
        ];
    }
}

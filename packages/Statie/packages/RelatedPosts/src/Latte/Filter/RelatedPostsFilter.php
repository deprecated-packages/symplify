<?php declare(strict_types=1);

namespace Symplify\Statie\RelatedPosts\Latte\Filter;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\RelatedPosts\RelatedPostsResolver;
use Symplify\Statie\Renderable\File\PostFile;

final class RelatedPostsFilter implements FilterProviderInterface
{
    /**
     * @var RelatedPostsResolver
     */
    private $relatedPostsResolver;

    public function __construct(RelatedPostsResolver $relatedPostsResolver)
    {
        $this->relatedPostsResolver = $relatedPostsResolver;
    }

    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // use in *.latte like this:
            // {var $relatedPosts = ($post|relatedPosts)}
            'relatedPosts' => function (PostFile $post) {
                return $this->relatedPostsResolver->resolveForPost($post);
            },

            // BC compatibility
            'similarPosts' => function (PostFile $post) {
                trigger_error(
                    '"{$post|similarPosts} Latte filter was deprecated '
                    . 'and will be removed in Statie 3.0. Use {$post|relatedPosts} instead."',
                    E_USER_DEPRECATED
                );
                sleep(3); // inspired at "deprecated interface" Tweet

                return $this->relatedPostsResolver->resolveForPost($post);
            },
        ];
    }
}

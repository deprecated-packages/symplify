<?php declare(strict_types=1);

class SomeClass
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // use in *.latte like this: {var $relatedPosts = ($post|relatedPosts)}
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

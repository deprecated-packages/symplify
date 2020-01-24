<?php

declare(strict_types=1);

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
        ];
    }
}

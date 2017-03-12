<?php declare(strict_types = 1);

namespace Symplify\Statie\Latte\Filter;

use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\PostFile;

final class SimilarPostsFilter implements LatteFiltersProviderInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'similarPosts' => function (PostFile $post, int $postCount) {
                return $this->getSimilarPosts($post, $postCount);
            }
        ];
        // TODO: Implement getFilters() method.
    }

    /**
     * @return PostFile[]
     */
    private function getSimilarPosts(PostFile $post, int $postCount): array
    {
        dump($this->configuration->getGlobalVariables());
        dump($post, $postCount);
        die;
    }

    // arguments post
    // insert posts ocnfiguration
    // find similar posts => x

    // http://php.net/manual/en/function.similar-text.php
}
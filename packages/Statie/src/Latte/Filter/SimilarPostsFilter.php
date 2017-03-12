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
    }

    /**
     * @return PostFile[]
     */
    private function getSimilarPosts(PostFile $mainPost, int $postCount): array
    {
        /** @var PostFile[] $posts */
        $posts = $this->configuration->getGlobalVariables()['posts'];

        $similarityMap = [];
        foreach ($posts as $post) {
            $similarityScore = $this->countSimilarityScore($mainPost, $post);
            $similarityMap[$similarityScore] = $post;
        }

        ksort($similarityMap);

        // skip the first, it's the same post
        $similarityMap = array_slice($similarityMap, 1, $postCount + 1);

        return $similarityMap;
    }

    private function countSimilarityScore(PostFile $mainPost, PostFile $post): int
    {
        $similarityScore = 0;

        if (isset($mainPost['title'], $post['title'])) {
            $titleScore = similar_text($mainPost['title'], $post['title']);
            $similarityScore += $titleScore * 5;
        }

        $similarityScore += similar_text($mainPost->getContent(), $post->getContent());

        return $similarityScore;
    }
}
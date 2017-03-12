<?php declare(strict_types=1);

namespace Symplify\Statie\Metrics;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\PostFile;

final class SimilarPostsResolver
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var PostSimilarityAnalyzer
     */
    private $postSimilarityAnalyzer;

    public function __construct(Configuration $configuration, PostSimilarityAnalyzer $postSimilarityAnalyzer)
    {
        $this->configuration = $configuration;
        $this->postSimilarityAnalyzer = $postSimilarityAnalyzer;
    }

    /**
     * @return PostFile[]
     */
    public function resolveForPostWithLimit(PostFile $mainPost, int $maxPostCount): array
    {
        $similarityMap = $this->buildSimilarityMap($mainPost);

        return array_slice($similarityMap, 0, $maxPostCount);
    }

    /**
     * @return PostFile[]
     */
    private function buildSimilarityMap(PostFile $mainPost): array
    {
        $map = [];
        foreach ($this->getPosts() as $post) {
            if ($this->arePostsIdentical($mainPost, $post)) {
                continue;
            }

            $score = $this->postSimilarityAnalyzer->analyzeTwoPosts($mainPost, $post);
            $map[$score] = $post;
        }

        krsort($map);

        return $map;
    }

    /**
     * @return PostFile[]
     */
    private function getPosts(): array
    {
        return $this->configuration->getGlobalVariables()['posts'];
    }

    private function arePostsIdentical(PostFile $firstPost, PostFile $secondPost): bool
    {
        return $firstPost->getBaseName() === $secondPost->getBaseName();
    }
}

<?php declare(strict_types=1);

namespace Symplify\Statie\Metrics;

use Symplify\Statie\Renderable\File\PostFile;

final class PostSimilarityAnalyzer
{
    /**
     * @var int
     */
    private const TITLE_WEIGHT = 5;

    public function analyzeTwoPosts(PostFile $firstPost, PostFile $secondPost): int
    {
        return $this->analyzeTitles($firstPost, $secondPost)
            + $this->analyzeContents($firstPost, $secondPost);
    }

    private function analyzeTitles(PostFile $firstPost, PostFile $secondPost): int
    {
        $score = 0;
        if (isset($firstPost['title'], $secondPost['title'])) {
            similar_text($firstPost['title'], $secondPost['title'], $similarityScore);
            $score += $similarityScore * self::TITLE_WEIGHT;
        }

        return $score;
    }

    private function analyzeContents(PostFile $firstPost, PostFile $secondPost)
    {
        similar_text($firstPost->getContent(), $secondPost->getContent(), $similarityScore);

        return (int) $similarityScore;
    }
}

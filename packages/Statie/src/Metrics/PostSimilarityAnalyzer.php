<?php declare(strict_types=1);

namespace Symplify\Statie\Metrics;

use Symplify\Statie\Renderable\File\PostFile;
use TextAnalysis\Comparisons\CosineSimilarityComparison;

final class PostSimilarityAnalyzer
{
    /**
     * @var CosineSimilarityComparison
     */
    private $cosineSimilarityComparison;

    public function __construct(CosineSimilarityComparison $cosineSimilarityComparison)
    {
        $this->cosineSimilarityComparison = $cosineSimilarityComparison;
    }

    public function analyzeTwoPosts(PostFile $firstPost, PostFile $secondPost): int
    {
        $relativeScore = $this->cosineSimilarityComparison->similarity(
            explode(' ', $firstPost->getContent()),
            explode(' ', $secondPost->getContent())
        );

        return (int) ($relativeScore * 100);
    }
}

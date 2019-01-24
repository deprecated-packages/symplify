<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\TweetFilter;

use Symplify\Statie\Tweeter\Tweet\PostTweet;
use Symplify\Statie\Tweeter\Tweet\PublishedTweet;

final class PublishedTweetsFilter
{
    /**
     * @param PostTweet[] $allTweets
     * @param PublishedTweet[] $publishedTweets
     * @return PostTweet[]
     */
    public function filter(array $allTweets, array $publishedTweets): array
    {
        return array_filter($allTweets, function ($tweet) use ($publishedTweets) {
            // true if unpublished, false if published
            return ! $this->wasTweetPublished($tweet, $publishedTweets);
        });
    }

    /**
     * @param PublishedTweet[] $publishedTweets
     */
    private function wasTweetPublished(PostTweet $postTweet, array $publishedTweets): bool
    {
        foreach ($publishedTweets as $publishedTweet) {
            if ($postTweet->isSimilarToPublishedTweet($publishedTweet)) {
                return true;
            }
        }

        return false;
    }
}

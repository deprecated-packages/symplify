<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\TweetFilter;

use Symplify\Statie\Tweeter\Tweet\PostTweet;
use Symplify\Statie\Tweeter\Tweet\PublishedTweet;
use Symplify\Statie\Tweeter\TwitterApi\TwitterApiWrapper;

final class PublishedTweetsFilter
{
    /**
     * @var PublishedTweet[]
     */
    private $publishedTweets = [];

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    public function __construct(TwitterApiWrapper $twitterApiWrapper)
    {
        $this->twitterApiWrapper = $twitterApiWrapper;
    }

    /**
     * @param PostTweet[] $allTweets
     * @return PostTweet[]
     */
    public function filter(array $allTweets): array
    {
        return array_filter($allTweets, function ($tweet): bool {
            // true if unpublished, false if published
            return ! $this->wasTweetPublished($tweet);
        });
    }

    private function wasTweetPublished(PostTweet $postTweet): bool
    {
        foreach ($this->getPublishedTweets() as $publishedTweet) {
            if ($postTweet->isSimilarToPublishedTweet($publishedTweet)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PublishedTweet[]
     */
    private function getPublishedTweets(): array
    {
        if ($this->publishedTweets) {
            return $this->publishedTweets;
        }

        return $this->publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
    }
}

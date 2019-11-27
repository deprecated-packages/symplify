<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\TweetFilter;

use Symplify\Statie\Tweeter\Tweet\PostTweet;

final class TweetsFilter
{
    /**
     * @var OldTweetsFilter
     */
    private $oldTweetsFilter;

    /**
     * @var PublishedTweetsFilter
     */
    private $publishedTweetsFilter;

    public function __construct(OldTweetsFilter $oldTweetsFilter, PublishedTweetsFilter $publishedTweetsFilter)
    {
        $this->oldTweetsFilter = $oldTweetsFilter;
        $this->publishedTweetsFilter = $publishedTweetsFilter;
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        $postTweets = $this->oldTweetsFilter->filter($postTweets);

        return $this->publishedTweetsFilter->filter($postTweets);
    }
}

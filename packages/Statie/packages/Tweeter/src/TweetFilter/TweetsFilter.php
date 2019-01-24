<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\TweetFilter;

use Symplify\Statie\Tweeter\Tweet\PostTweet;
use Symplify\Statie\Tweeter\TwitterApi\TwitterApiWrapper;

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

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    public function __construct(
        OldTweetsFilter $oldTweetsFilter,
        PublishedTweetsFilter $publishedTweetsFilter,
        TwitterApiWrapper $twitterApiWrapper
    ) {
        $this->oldTweetsFilter = $oldTweetsFilter;
        $this->publishedTweetsFilter = $publishedTweetsFilter;
        $this->twitterApiWrapper = $twitterApiWrapper;
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        $postTweets = $this->oldTweetsFilter->filter($postTweets);
        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();

        return $this->publishedTweetsFilter->filter($postTweets, $publishedTweets);
    }
}

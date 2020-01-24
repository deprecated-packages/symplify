<?php

declare(strict_types=1);

namespace Symplify\Statie\Tweeter\TweetFilter;

use DateTimeInterface;
use Nette\Utils\DateTime;
use Symplify\Statie\Tweeter\Tweet\PostTweet;

final class OldTweetsFilter
{
    /**
     * @var DateTimeInterface
     */
    private $maxPastDateTime;

    public function __construct(int $twitterMaximalDaysInPast)
    {
        $this->maxPastDateTime = DateTime::from('-' . $twitterMaximalDaysInPast . 'days');
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        return array_filter($postTweets, function (PostTweet $postTweet): bool {
            return $postTweet->getPostDateTime() >= $this->maxPastDateTime;
        });
    }
}

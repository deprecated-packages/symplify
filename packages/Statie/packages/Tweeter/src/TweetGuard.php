<?php

declare(strict_types=1);

namespace Symplify\Statie\Tweeter;

use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Tweeter\Configuration\Keys;
use Symplify\Statie\Tweeter\Exception\TweetImageNotFoundException;
use Symplify\Statie\Tweeter\Exception\TweetTooLongException;

final class TweetGuard
{
    /**
     * @var int
     */
    private const TWEET_MAX_LENGTH = 280;

    /**
     * @var int
     * @see https://dev.twitter.com/basics/tco#how-do-i-calculate-if-a-tweet-with-a-link-is-going-to-be-over-140-characters-or-not
     */
    private const SHORTENED_URL_LENGTH = 23;

    public function ensureTweetFitsAllowedLength(string $tweet, PostFile $postFile): void
    {
        $tweetLength = mb_strlen($tweet);
        if ($tweetLength <= self::TWEET_MAX_LENGTH) {
            return;
        }

        throw new TweetTooLongException(sprintf(
            'Tweet message "%s" is too long, after adding its url. It has %d chars, shorten it under %d.' .
            PHP_EOL .
            PHP_EOL .
            'Look to "%s" file.',
            $tweet,
            $tweetLength,
            self::TWEET_MAX_LENGTH - self::SHORTENED_URL_LENGTH,
            realpath($postFile->getFilePath())
        ));
    }

    public function ensureTweetImageExists(PostFile $postFile, string $localFilePath): void
    {
        if (file_exists($localFilePath)) {
            return;
        }

        throw new TweetImageNotFoundException(sprintf(
            'Tweet image "%s" for "%s" file not found. Check "%s" option.',
            $localFilePath,
            realpath($postFile->getFilePath()),
            Keys::TWEET_IMAGE
        ));
    }
}

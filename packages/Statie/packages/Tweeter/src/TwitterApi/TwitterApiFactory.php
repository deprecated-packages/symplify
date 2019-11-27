<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\TwitterApi;

use TwitterAPIExchange;

/**
 * This factory only allows to use ENV variable without any config dependency
 */
final class TwitterApiFactory
{
    /**
     * @var string
     */
    private $twitterConsumerKey;

    /**
     * @var string
     */
    private $twitterConsumerSecret;

    /**
     * @var string
     */
    private $twitterOauthAccessToken;

    /**
     * @var string
     */
    private $twitterOauthAccessTokenSecret;

    public function __construct(
        string $twitterConsumerKey,
        string $twitterConsumerSecret,
        string $twitterOauthAccessToken,
        string $twitterOauthAccessTokenSecret
    ) {
        $this->twitterConsumerKey = $twitterConsumerKey;
        $this->twitterConsumerSecret = $twitterConsumerSecret;
        $this->twitterOauthAccessToken = $twitterOauthAccessToken;
        $this->twitterOauthAccessTokenSecret = $twitterOauthAccessTokenSecret;
    }

    public function create(): TwitterAPIExchange
    {
        return new TwitterAPIExchange([
            'consumer_key' => $this->twitterConsumerKey,
            'consumer_secret' => $this->twitterConsumerSecret,
            'oauth_access_token' => $this->twitterOauthAccessToken,
            'oauth_access_token_secret' => $this->twitterOauthAccessTokenSecret,
        ]);
    }
}

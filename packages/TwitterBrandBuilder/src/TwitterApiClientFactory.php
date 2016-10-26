<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\TwitterBrandBuilder;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symplify\TwitterBrandBuilder\Hydrator\MediaHydrator;
use Symplify\TwitterBrandBuilder\Hydrator\TweetHydrator;

// todo: turn to DI APP, so many stuffs :)
// nette di etc.

final class TwitterApiClientFactory
{
    public function create(
        string $consumerKey,
        string $consumerSecret,
        string $oauthAccessToken,
        string $oauthAccessTokenSecret
    ) : TwitterApiClient {
        $connection = new TwitterOAuth($consumerKey, $consumerSecret, $oauthAccessToken, $oauthAccessTokenSecret);

        return new TwitterApiClient($connection, new TweetHydrator(), new MediaHydrator());
    }
}

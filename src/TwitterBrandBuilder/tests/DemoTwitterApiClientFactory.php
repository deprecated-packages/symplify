<?php

declare(strict_types=1);

namespace Symplify\TwitterBrandBuilder\Tests;

use Symplify\TwitterBrandBuilder\TwitterApiClient;
use Symplify\TwitterBrandBuilder\TwitterApiClientFactory;

final class DemoTwitterApiClientFactory
{
    public function createTwitterApiClient() : TwitterApiClient
    {
        return (new TwitterApiClientFactory())->create(
            AccessCredentials::CONSUMER_KEY,
            AccessCredentials::CONSUMER_SECRET,
            AccessCredentials::OAUTH_ACCESS_TOKEN,
            AccessCredentials::OAUTH_ACCESS_TOKEN_SECRET
        );
    }
}

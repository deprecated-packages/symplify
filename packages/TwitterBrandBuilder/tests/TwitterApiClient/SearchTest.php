<?php

declare(strict_types=1);

namespace Symplify\TwitterBrandBuilder\Tests\TwitterApiClient;

use PHPUnit\Framework\TestCase;
use Symplify\TwitterBrandBuilder\Tests\DemoTwitterApiClientFactory;
use Symplify\TwitterBrandBuilder\TwitterApiClient;

final class SearchTest extends TestCase
{
    /**
     * @var TwitterApiClient
     */
    private $twitterApiClient;

    protected function setUp()
    {
        $this->twitterApiClient = (new DemoTwitterApiClientFactory())->createTwitterApiClient();
    }

    public function testSearchHashtag()
    {
        $this->markTestSkipped('slow');

        $tweets = $this->twitterApiClient->searchHashtag('twitter');
        $this->assertGreaterThan(7, $tweets);
    }
}

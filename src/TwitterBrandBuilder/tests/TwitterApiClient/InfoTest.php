<?php

declare(strict_types=1);

namespace Symplify\TwitterBrandBuilder\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\TwitterBrandBuilder\TwitterApiClient;

final class InfoTest extends TestCase
{
    /**
     * @var TwitterApiClient
     */
    private $twitterApiClient;

    protected function setUp()
    {
        $this->twitterApiClient = (new DemoTwitterApiClientFactory())->createTwitterApiClient();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(TwitterApiClient::class, $this->twitterApiClient);
    }

    public function testGetMentions()
    {
        $this->markTestSkipped('slow');

        $tweets = $this->twitterApiClient->getMyMentions();
        $this->assertContains('@j7php Test mention', $tweets[0]->getText());
    }

    public function testGetUserTimeline()
    {
        $this->markTestSkipped('slow');

        $tweets = $this->twitterApiClient->getUserTimeline(3232926711);
        $this->assertContains('Test Tweet', $tweets[count($tweets) - 1]->getText());
    }

    public function testGetRetweetsOfMe()
    {
        $this->markTestSkipped('slow');

        $tweets = $this->twitterApiClient->getRetcweetsOfMe();
        $this->assertContains('travis CI and tests', $tweets[0]->getText());
    }

    public function testGetRetweetsOfTweet()
    {
        $this->markTestSkipped('slow');

        $tweets = $this->twitterApiClient->getRetweetsOfTweet(595155660494471168);
        $this->assertContains('travis CI and tests', $tweets[0]->getText());
    }

    public function testStatusesShowId()
    {
        $tweet = $this->twitterApiClient->getTweet(595155660494471168);
        $this->assertContains('travis CI and tests', $tweet->getText());
    }
}

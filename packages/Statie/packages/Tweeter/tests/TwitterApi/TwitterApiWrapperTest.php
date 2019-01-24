<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests\TwitterApi;

use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tweeter\Tweet\PublishedTweet;
use Symplify\Statie\Tweeter\TwitterApi\TwitterApiWrapper;

final class TwitterApiWrapperTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    protected function setUp(): void
    {
        $this->twitterApiWrapper = $this->container->get(TwitterApiWrapper::class);
    }

    public function testGetPublishedTweets(): void
    {
        if (getenv('TWITTER_CONSUMER_KEY') === false) {
            $this->markTestSkipped('Run Twitter test only with access tokens.');
        }

        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
        $this->assertGreaterThanOrEqual(41, count($publishedTweets));

        $this->assertInstanceOf(PublishedTweet::class, $publishedTweets[0]);
    }
}

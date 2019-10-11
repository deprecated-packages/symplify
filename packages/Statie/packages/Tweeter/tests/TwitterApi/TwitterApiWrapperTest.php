<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests\TwitterApi;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Tweeter\Tweet\PublishedTweet;
use Symplify\Statie\Tweeter\TwitterApi\TwitterApiWrapper;

final class TwitterApiWrapperTest extends AbstractKernelTestCase
{
    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);

        $this->twitterApiWrapper = self::$container->get(TwitterApiWrapper::class);
    }

    public function testGetPublishedTweets(): void
    {
        if (getenv('TWITTER_CONSUMER_KEY') === false) {
            $this->markTestSkipped('Run Twitter test only with access tokens.');
        }

        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
        $this->assertGreaterThanOrEqual(20, count($publishedTweets));

        $this->assertInstanceOf(PublishedTweet::class, $publishedTweets[0]);
    }
}

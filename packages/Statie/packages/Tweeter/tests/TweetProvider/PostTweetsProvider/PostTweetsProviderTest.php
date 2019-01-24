<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests\TweetProvider\PostTweetsProvider;

use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tweeter\TweetProvider\PostTweetsProvider;

final class PostTweetsProviderTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    protected function setUp(): void
    {
        $statieConfiguration = $this->container->get(StatieConfiguration::class);
        $statieConfiguration->setSourceDirectory(__DIR__ . '/Source');

        $this->postTweetsProvider = $this->container->get(PostTweetsProvider::class);
    }

    public function testPostDateTime(): void
    {
        $postTweets = $this->postTweetsProvider->provide();

        $this->assertCount(1, $postTweets);
        $tweet = $postTweets[0];

        $postDate = $tweet->getPostDateTime()->format('Y-m-d');

        $this->assertSame('2019-01-30', $postDate);
    }
}

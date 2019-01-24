<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests;

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
        parent::setUp();

        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/Source');

        $this->postTweetsProvider = $this->container->get(PostTweetsProvider::class);
    }

    public function test(): void
    {
        $postTweets = $this->postTweetsProvider->provide();

        $this->assertCount(1, $postTweets);

        $postTweet = $postTweets[0];

        $postDate = $postTweet->getPostDateTime()->format('Y-m-d');
        $this->assertSame('2018-10-30', $postDate);
    }
}

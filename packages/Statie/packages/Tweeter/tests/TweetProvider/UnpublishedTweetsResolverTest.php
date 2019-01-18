<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests\TweetProvider;

use Nette\Utils\FileSystem;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tweeter\Tweet\Tweet;
use Symplify\Statie\Tweeter\TweetProvider\PostTweetsProvider;
use Symplify\Statie\Tweeter\TweetProvider\UnpublishedTweetsResolver;
use Symplify\Statie\Tweeter\TwitterApi\TwitterApiWrapper;
use Twig\Loader\ArrayLoader;

final class UnpublishedTweetsResolverTest extends AbstractContainerAwareTestCase
{
    /**
     * @var UnpublishedTweetsResolver
     */
    private $unpublishedTweetsResolver;

    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/Source');

        $this->postTweetsProvider = $this->container->get(PostTweetsProvider::class);
        $this->twitterApiWrapper = $this->container->get(TwitterApiWrapper::class);
        $this->unpublishedTweetsResolver = $this->container->get(UnpublishedTweetsResolver::class);

        // set twig templates
        $arrayLoader = $this->container->get(ArrayLoader::class);
        $arrayLoader->setTemplate('_layouts/post.twig', FileSystem::read(__DIR__ . '/Source/_layouts/post.twig'));
    }

    public function testUnpublishedTweetsResolver(): void
    {
        if (getenv('TWITTER_CONSUMER_KEY') === false) {
            $this->markTestSkipped('Run Twitter test only with access tokens.');
        }

        $unpublishedTweets = $this->unpublishedTweetsResolver->excludePublishedTweets(
            $this->postTweetsProvider->provide(),
            $this->twitterApiWrapper->getPublishedTweets()
        );

        foreach ($unpublishedTweets as $unpublishedTweet) {
            // this tweet is already published, so it should not be here
            $this->assertNotContains(
                'New post on my blog: Clean and Decoupled Controllers, Commands and Event Subscribers ',
                $unpublishedTweet->getText()
            );
        }

        $this->assertCount(1, $unpublishedTweets);
    }

    public function testPostTweetsProvider(): void
    {
        $postTweets = $this->postTweetsProvider->provide();
        $this->assertCount(1, $postTweets);

        $this->assertInstanceOf(Tweet::class, $postTweets[0]);
    }

    public function testTwitterApiWrapper(): void
    {
        if (getenv('TWITTER_CONSUMER_KEY') === false) {
            $this->markTestSkipped('Run Twitter test only with access tokens.');
        }

        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
        $this->assertGreaterThanOrEqual(41, count($publishedTweets));

        $this->assertInstanceOf(Tweet::class, $publishedTweets[0]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/tweet-provider-statie.yml';
    }
}

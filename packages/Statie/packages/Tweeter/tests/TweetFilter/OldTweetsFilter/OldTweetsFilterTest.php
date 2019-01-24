<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests\TweetFilter\OldTweetsFilter;

use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Tweeter\TweetFilter\OldTweetsFilter;
use Symplify\Statie\Tweeter\TweetProvider\PostTweetsProvider;

final class OldTweetsFilterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    /**
     * @var OldTweetsFilter
     */
    private $oldTweetsFilter;

    protected function setUp(): void
    {
        parent::setUp();

        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/../../Source');

        $this->postTweetsProvider = $this->container->get(PostTweetsProvider::class);
        $this->oldTweetsFilter = $this->container->get(OldTweetsFilter::class);
    }

    public function test(): void
    {
        $postTweets = $this->postTweetsProvider->provide();
        $this->assertCount(1, $postTweets);

        $postTweets = $this->oldTweetsFilter->filter($postTweets);
        $this->assertCount(0, $postTweets);
    }
}

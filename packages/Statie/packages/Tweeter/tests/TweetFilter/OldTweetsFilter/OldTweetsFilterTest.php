<?php declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests\TweetFilter\OldTweetsFilter;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Tweeter\TweetFilter\OldTweetsFilter;
use Symplify\Statie\Tweeter\TweetProvider\PostTweetsProvider;

final class OldTweetsFilterTest extends AbstractKernelTestCase
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
        $this->bootKernel(StatieKernel::class);

        parent::setUp();

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/../../Source');

        $this->postTweetsProvider = self::$container->get(PostTweetsProvider::class);
        $this->oldTweetsFilter = self::$container->get(OldTweetsFilter::class);
    }

    public function test(): void
    {
        $postTweets = $this->postTweetsProvider->provide();
        $this->assertCount(1, $postTweets);

        $postTweets = $this->oldTweetsFilter->filter($postTweets);
        $this->assertCount(0, $postTweets);
    }
}

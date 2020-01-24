<?php

declare(strict_types=1);

namespace Symplify\Statie\Tweeter\Tests;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Tweeter\TweetProvider\PostTweetsProvider;

final class PostTweetsProviderTest extends AbstractKernelTestCase
{
    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);

        parent::setUp();

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/Source');

        $this->postTweetsProvider = self::$container->get(PostTweetsProvider::class);
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

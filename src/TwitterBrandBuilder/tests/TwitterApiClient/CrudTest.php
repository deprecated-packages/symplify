<?php

declare(strict_types=1);

namespace Symplify\TwitterBrandBuilder\Tests\TwitterApiClient;

use PHPUnit\Framework\TestCase;
use Symplify\TwitterBrandBuilder\Tests\DemoTwitterApiClientFactory;
use Symplify\TwitterBrandBuilder\TwitterApiClient;

final class CrudTest extends TestCase
{
    /**
     * @var TwitterApiClient
     */
    private $twitterApiClient;

    protected function setUp()
    {
        $this->twitterApiClient = (new DemoTwitterApiClientFactory())->createTwitterApiClient();
    }

    public function testMediaUpload()
    {
        $this->markTestSkipped('slow');

        $media = $this->twitterApiClient->uploadMedia(__DIR__ . '/CrudSource/img.png');
        $this->assertContains('image/png', $media->getImageType());

        return $media->getId();
    }

    /**
     * @depends testMediaUpload
     */
    public function testUpdate(int $mediaId)
    {
        $tweet = $this->twitterApiClient->update(
            'TEST TWEET TO BE DELETED' . rand(),
            [$mediaId]
        );
        $this->assertContains('TEST TWEET TO BE DELETED', $tweet->getText());

        return $tweet->getId();
    }

    /**
     * @depends testUpdate
     */
    public function testStatusesDestroy(int $tweetId)
    {
        $tweet = $this->twitterApiClient->delete($tweetId);
        $this->assertContains('TEST TWEET TO BE DELETED', $tweet->getText());
    }
}

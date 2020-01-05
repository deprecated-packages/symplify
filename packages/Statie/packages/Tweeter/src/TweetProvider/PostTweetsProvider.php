<?php

declare(strict_types=1);

namespace Symplify\Statie\Tweeter\TweetProvider;

use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Templating\LayoutsAndSnippetsLoader;
use Symplify\Statie\Tweeter\Configuration\Keys;
use Symplify\Statie\Tweeter\Tweet\PostTweet;
use Symplify\Statie\Tweeter\TweetGuard;

final class PostTweetsProvider
{
    /**
     * @var string
     */
    private $siteUrl;

    /**
     * @var TweetGuard
     */
    private $tweetGuard;

    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var LayoutsAndSnippetsLoader
     */
    private $layoutsAndSnippetsLoader;

    public function __construct(
        string $siteUrl,
        StatieConfiguration $statieConfiguration,
        TweetGuard $tweetGuard,
        Generator $generator,
        LayoutsAndSnippetsLoader $layoutsAndSnippetsLoader
    ) {
        $this->siteUrl = $siteUrl;
        $this->tweetGuard = $tweetGuard;
        $this->statieConfiguration = $statieConfiguration;
        $this->generator = $generator;
        $this->layoutsAndSnippetsLoader = $layoutsAndSnippetsLoader;
    }

    /**
     * @return PostTweet[]
     */
    public function provide(): array
    {
        $this->layoutsAndSnippetsLoader->loadFromSource($this->statieConfiguration->getSourceDirectory());

        $postTweets = [];
        foreach ($this->getPosts() as $post) {
            $postConfiguration = $post->getConfiguration();
            if (! isset($postConfiguration[Keys::TWEET])) {
                continue;
            }

            $rawTweetText = $postConfiguration[Keys::TWEET];
            $this->tweetGuard->ensureTweetFitsAllowedLength($rawTweetText, $post);

            $postTweet = $this->appendAbsoluteUrlToTweet($post, $rawTweetText);
            $tweetImage = $this->resolveTweetImage($post, $postConfiguration);

            $postTweets[] = new PostTweet($postTweet, $post->getDate(), $tweetImage);
        }

        return $postTweets;
    }

    /**
     * @return PostFile[]
     */
    private function getPosts(): array
    {
        if ($this->statieConfiguration->getOption('posts') === null) {
            $this->generator->run();
        }

        return $this->statieConfiguration->getOption('posts') ?? [];
    }

    private function appendAbsoluteUrlToTweet(PostFile $postFile, string $rawTweetText): string
    {
        $url = $this->getAbsoluteUrlForPost($postFile);

        return $rawTweetText . ' ' . $url . '/';
    }

    /**
     * @param mixed[] $postConfiguration
     */
    private function resolveTweetImage(PostFile $postFile, array $postConfiguration): ?string
    {
        if (! isset($postConfiguration[Keys::TWEET_IMAGE])) {
            return null;
        }

        $localFilePath = $this->statieConfiguration->getSourceDirectory() . $postConfiguration[Keys::TWEET_IMAGE];

        $this->tweetGuard->ensureTweetImageExists($postFile, $localFilePath);

        return $this->siteUrl . '/' . $postConfiguration[Keys::TWEET_IMAGE];
    }

    private function getAbsoluteUrlForPost(PostFile $postFile): string
    {
        return $this->siteUrl . '/' . $postFile->getRelativeUrl();
    }
}

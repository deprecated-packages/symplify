<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\TwitterBrandBuilder;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symplify\TwitterBrandBuilder\Entity\Media;
use Symplify\TwitterBrandBuilder\Entity\Tweet;
use Symplify\TwitterBrandBuilder\Entity\User;
use Symplify\TwitterBrandBuilder\Filter\FavoriteSorter;
use Symplify\TwitterBrandBuilder\Hydrator\MediaHydrator;
use Symplify\TwitterBrandBuilder\Hydrator\TweetHydrator;

final class TwitterApiClient
{
    /**
     * @var TwitterOAuth
     */
    private $connection;

    /**
     * @var TweetHydrator
     */
    private $tweetHydrator;

    /**
     * @var MediaHydrator
     */
    private $mediaHydrator;

    /**
     * @var User
     */
    private $user;

    public function __construct(TwitterOAuth $connection, TweetHydrator $tweetHydrator, MediaHydrator $mediaHydrator)
    {
        $this->connection = $connection;
        $this->tweetHydrator = $tweetHydrator;
        $this->mediaHydrator = $mediaHydrator;
    }

    /**
     * See https://dev.twitter.com/rest/reference/get/statuses/mentions_timeline.
     *
     * @return Tweet[]
     */
    public function getMyMentions() : array
    {
        $tweets = $this->connection->get('statuses/mentions_timeline');

        return $this->tweetHydrator->hydrateList($tweets);
    }

    /**
     * See https://dev.twitter.com/rest/reference/get/statuses/user_timeline.
     *
     * @return Tweet[]
     */
    public function getUserTimeline(int $userId) : array
    {
        $tweets = $this->connection->get('statuses/user_timeline', [
            'user_id' => $userId,
        ]);

        return $this->tweetHydrator->hydrateList($tweets);
    }

    /**
     * See https://dev.twitter.com/rest/reference/get/statuses/retweets_of_me.
     *
     * @return Tweet[]
     */
    public function getRetweetsOfMe() : array
    {
        $tweets = $this->connection->get('statuses/retweets_of_me');

        return $this->tweetHydrator->hydrateList($tweets);
    }

    /**
     * @see https://api.twitter.com/1.1/statuses/retweets/:id.json
     *
     * @return Tweet[]
     */
    public function getRetweetsOfTweet(int $tweetId) : array
    {
        $tweets = $this->connection->get('statuses/retweets', [
            'id' => $tweetId,
        ]);

        return $this->tweetHydrator->hydrateList($tweets);
    }

    /**
     * @see https://dev.twitter.com/rest/reference/get/statuses/show/:id
     */
    public function getTweet(int $tweetId) : Tweet
    {
        $tweet = $this->connection->get('statuses/show', [
            'id' => $tweetId,
        ]);

        return $this->tweetHydrator->hydrateSingle($tweet);
    }

    /**
     * See https://dev.twitter.com/rest/reference/get/search/tweets.
     *
     * @return Tweet[]
     */
    public function searchHashtag(string $hashtag) : array
    {
        $searchResult = $this->connection->get('search/tweets', [
            'q' => '#' . $hashtag,
        ]);

        return $this->tweetHydrator->hydrateList($searchResult->statuses);
    }

    /**
     * See https://dev.twitter.com/rest/reference/post/media/upload.
     *
     * Note: Uploaded unattached media files will be available for attachment to a tweet for 60 minutes
     */
    public function uploadMedia(string $filePath) : Media
    {
        $uploadedData = $this->connection->upload('media/upload', [
            'media' => $filePath,
        ]);

        return $this->mediaHydrator->hydrateSingle($uploadedData);
    }

    /**
     * See https://dev.twitter.com/rest/reference/post/statuses/update.
     */
    public function update(string $message, array $mediaIds = []) : Tweet
    {
        $tweet = $this->connection->post('statuses/update', [
            'status' => $message,
            'possibly_sensitive' => false,
            'media_ids' => $mediaIds,
        ]);

        return $this->tweetHydrator->hydrateSingle($tweet);
    }

    /**
     * See https://dev.twitter.com/rest/reference/post/statuses/destroy/:id.
     */
    public function delete(int $tweetId) : Tweet
    {
        $tweet = $this->connection->post('statuses/destroy', [
            'id' => $tweetId,
        ]);

        return $this->tweetHydrator->hydrateSingle($tweet);
    }

    /**
     * Mimic https://analytics.twitter.com/user/VotrubaT/home.
     *
     * @return Tweet[]
     */
    public function getMyMostActiveTweets() : array
    {
        $tweets = $this->connection->get('statuses/user_timeline', [
            'user_id' => $this->getUserId(),
            'count' => 200,
            'exclude_replies' => true,
            'include_rts' => false,
        ]);

        $tweets = $this->tweetHydrator->hydrateList($tweets);

        return (new FavoriteSorter())->sortTweets($tweets);
    }

    /**
     * @return Tweet[]
     */
    public function getMostPopularTweetsByTopic(string $topic) : array
    {
        $searchResult = $this->connection->get('search/tweets', [
            'q' => $topic,
            'result_type' => 'popular',
        ]);

        return $this->tweetHydrator->hydrateList($searchResult->statuses);
    }

    private function getUserId() : int
    {
        return $this->getUser()->getId();
    }

    private function getUser() : User
    {
        if ($this->user) {
            return $this->user;
        }

        $user = new User($this->connection->get('account/verify_credentials'));

        return $this->user = $user;
    }
}

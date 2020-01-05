<?php

declare(strict_types=1);

namespace Symplify\Statie\Tweeter;

final class TweetEntityCompleter
{
    /**
     * @param string[] $tweets
     * @return mixed[]
     */
    public function completeOriginalUrlsToText(array $tweets): array
    {
        foreach ($tweets as $key => $tweet) {
            $entities = $tweet['entities'];
            if (count($entities['urls']) === 0) {
                continue;
            }

            $tweets[$key]['text'] = $this->replaceShortUrlsWithOriginalOnes($entities, $tweet['text']);
        }

        return $tweets;
    }

    /**
     * @param mixed[] $entities
     */
    private function replaceShortUrlsWithOriginalOnes(array $entities, string $tweetText): string
    {
        foreach ($entities['urls'] as $url) {
            $tweetText = str_replace($url['url'], $url['expanded_url'], $tweetText);
        }

        return $tweetText;
    }
}

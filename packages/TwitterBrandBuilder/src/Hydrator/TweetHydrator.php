<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\TwitterBrandBuilder\Hydrator;

use stdClass;
use Symplify\TwitterBrandBuilder\Entity\Tweet;

final class TweetHydrator
{
    /**
     * @return Tweet[]
     */
    public function hydrateList(array $stdClassTweets) : array
    {
        $tweets = [];
        foreach ($stdClassTweets as $id => $stdClassTweet) {
            $tweets[$id] = $this->hydrateSingle($stdClassTweet);
        }

        return $tweets;
    }

    public function hydrateSingle(stdClass $stdClassTweet) : Tweet
    {
        return new Tweet($stdClassTweet);
    }
}

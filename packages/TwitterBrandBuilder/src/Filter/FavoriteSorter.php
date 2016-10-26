<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\TwitterBrandBuilder\Filter;

use Symplify\TwitterBrandBuilder\Entity\Tweet;

class FavoriteSorter
{
    /**
     * @param Tweet[] $tweets
     *
     * @return Tweet[]
     */
    public function sortTweets(array $tweets) : array
    {
        usort($tweets, function (Tweet $one, Tweet $two) {
            return $one->getRetweetCount() < $two->getRetweetCount();
        });

        return $tweets;
    }
}

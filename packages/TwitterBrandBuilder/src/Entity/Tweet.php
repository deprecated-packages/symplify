<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\TwitterBrandBuilder\Entity;

use stdClass;

final class Tweet
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $text;

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $retweetCount;

    /**
     * @var int
     */
    private $favoriteCount;

    public function __construct(stdClass $stdClass)
    {
        $this->id = $stdClass->id;
        $this->text = $stdClass->text;
        $this->user = new User($stdClass->user);
        $this->retweetCount = $stdClass->retweet_count;
        $this->favoriteCount = $stdClass->favorite_count;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getText() : string
    {
        return $this->text;
    }

    public function getUser() : User
    {
        return $this->user;
    }

    public function getRetweetCount() : int
    {
        return $this->retweetCount;
    }

    public function getFavoriteCount() : int
    {
        return $this->favoriteCount;
    }
}

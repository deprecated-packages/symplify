<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\TwitterBrandBuilder\Entity;

use stdClass;

final class Media
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var stdClass
     */
    private $image;

    /**
     * @var string
     */
    private $imageType;

    public function __construct(stdClass $stdClass)
    {
        $this->id = $stdClass->media_id;
        $this->image = $stdClass->image;

        if ($stdClass->image) {
            $this->imageType = $stdClass->image->image_type;
        }
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getImage() : stdClass
    {
        return $this->image;
    }

    public function getImageType() : string
    {
        return $this->imageType;
    }
}

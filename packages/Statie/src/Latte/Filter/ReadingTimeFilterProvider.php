<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Latte\Filter;

use Zenify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class ReadingTimeFilterProvider implements LatteFiltersProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters() : array
    {
        return [
            'readTimeInMinutes' => function (string $text) {
                return $this->readTimeInMinutes($text);
            },
        ];
    }

    private function readTimeInMinutes(string $text) : int
    {
        $wordCount = $this->wordCount($text);
        $minutesCount = ceil($wordCount / 260);

        return $minutesCount;
    }

    private function wordCount(string $text = null) : int
    {
        $text = strip_tags($text);
        $wordCount = count(explode(' ', $text));

        return $wordCount;
    }
}

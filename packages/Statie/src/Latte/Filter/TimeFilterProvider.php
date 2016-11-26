<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Latte\Filter;

use Zenify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class TimeFilterProvider implements LatteFiltersProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters() : array
    {
        return [
            'timeToSeconds' => function (string $time) {
                return $this->convertTimeToSeconds($time);
            },
        ];
    }

    private function convertTimeToSeconds(string $time) : int
    {
        sscanf($time, '%d:%d:%d', $hours, $minutes, $seconds);
        $seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        return (int) $seconds;
    }
}

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
            'readTimeInMinutes' => function (string $text, string $lang) {
                return $this->readTimeInMinutes($text, $lang);
            },
        ];
    }

    /**
     * @return string|void
     */
    private function readTimeInMinutes(string $text = null, string $lang = null)
    {
        if ($text === null) {
            return;
        }

        $wordCount = $this->wordCount($text);
        $minutesCount = ceil($wordCount / 260);

        switch ($minutesCount) {
            case 1:
                $minutesLocalized = $lang === 'en' ? 'minute' : 'minuta';
                break;
            case 2:
            case 3:
            case 4:
                $minutesLocalized = $lang === 'en' ? 'minutes' : 'minuty';
                break;
            default:
                $minutesLocalized = $lang === 'en' ? 'minutes' : 'minut';
                break;
        }

        return $minutesCount . ' ' . $minutesLocalized . ' ' . ($lang === 'en' ? 'of reading' : 'čtení');
    }

    private function wordCount(string $text = null) : int
    {
        $text = strip_tags($text);
        $wordCount = count(explode(' ', $text));

        return $wordCount;
    }
}

<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use DateTimeInterface;
use Nette\Utils\DateTime;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class TimeFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // usage in Twig: {% var in_days = diff_from_today_in_days(event.startDateTime) %}
            // usage in Latte: {var $inDays = ($event->getStartDateTime()|diff_from_today_in_days)}
            'diff_from_today_in_days' => function ($dateTime): int {
                $dateTime = $this->normalizeDateTime($dateTime);

                $dateInterval = $dateTime->diff(new DateTime('now'));
                if (! $dateInterval->invert) {
                    return (int) - $dateInterval->days;
                }

                return (int) $dateInterval->days;
            },

            'time_to_seconds' => function (string $time): int {
                return $this->convertTimeToSeconds($time);
            },
            // for BC
            'timeToSeconds' => function (string $time): int {
                return $this->convertTimeToSeconds($time);
            },
        ];
    }

    private function convertTimeToSeconds(string $time): int
    {
        sscanf($time, '%d:%d:%d', $hours, $minutes, $seconds);
        $seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

        return (int) $seconds;
    }

    /**
     * @param mixed $dateTime
     */
    private function normalizeDateTime($dateTime): DateTimeInterface
    {
        return DateTime::from($dateTime);
    }
}

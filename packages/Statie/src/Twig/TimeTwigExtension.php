<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use DateTimeInterface;
use Iterator;
use Nette\Utils\DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TimeTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): Iterator
    {
        yield new TwigFilter('time_to_seconds', function (string $time): int {
            return $this->convertTimeToSeconds($time);
        });
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): Iterator
    {
        // usage in Twig: {% var in_days = diff_from_today_in_days(event.startDateTime) %}
        yield new TwigFunction('diff_from_today_in_days', function ($dateTime): int {
            $dateTime = $this->normalizeDateTime($dateTime);

            $dateInterval = $dateTime->diff(new DateTime('now'));
            if ($dateInterval->invert === 0) {
                return (int) - $dateInterval->days;
            }

            return (int) $dateInterval->days;
        });
    }

    /**
     * @param mixed $dateTime
     */
    private function normalizeDateTime($dateTime): DateTimeInterface
    {
        return DateTime::from($dateTime);
    }

    private function convertTimeToSeconds(string $time): int
    {
        sscanf($time, '%d:%d:%d', $hours, $minutes, $seconds);
        $seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

        return (int) $seconds;
    }
}

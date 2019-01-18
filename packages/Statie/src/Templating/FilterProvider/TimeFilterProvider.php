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
        ];
    }

    /**
     * @param mixed $dateTime
     */
    private function normalizeDateTime($dateTime): DateTimeInterface
    {
        return DateTime::from($dateTime);
    }
}

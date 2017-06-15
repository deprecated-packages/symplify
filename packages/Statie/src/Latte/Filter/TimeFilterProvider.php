<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Filter;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class TimeFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // @todo: usage
            'timeToSeconds' => function (string $time) {
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
}

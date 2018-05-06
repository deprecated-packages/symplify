<?php declare(strict_types=1);

namespace Symplify\Statie\Utils;

use DateTime;
use DateTimeInterface;
use SplFileInfo;

final class PathAnalyzer
{
    /**
     * @var string
     */
    private const DATE_PATTERN = '(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})';

    /**
     * @var string
     */
    private const NAME_PATTERN = '(?<name>[a-zA-Z0-9-_]*)';

    public function detectDate(SplFileInfo $fileInfo): ?DateTimeInterface
    {
        preg_match('#' . self::DATE_PATTERN . '#', $fileInfo->getFilename(), $matches);

        if (count($matches) <= 3) {
            return null;
        }

        $date = sprintf('%d-%d-%d', $matches['year'], $matches['month'], $matches['day']);

        return new DateTime($date);
    }

    public function detectFilenameWithoutDate(SplFileInfo $fileInfo): string
    {
        $date = $this->detectDate($fileInfo);
        if ($date) {
            $dateAndNamePattern = sprintf('#%s-%s#', self::DATE_PATTERN, self::NAME_PATTERN);

            preg_match($dateAndNamePattern, $fileInfo->getFilename(), $matches);

            return $matches['name'];
        }

        return $fileInfo->getBasename('.' . $fileInfo->getExtension());
    }
}

<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Utils;

use Nette\Utils\Strings;

/**
 * @see \Symplify\Psr4Switcher\Tests\Utils\SymplifyStringsTest
 */
final class SymplifyStrings
{
    private ?int $lastSlashPosition = null;

    /**
     * Same as ↓, just for the suffix
     *
     * @see \Nette\Utils\Strings::findPrefix()
     *
     * @param string[] $strings
     */
    public function findSharedSlashedSuffix(array $strings): string
    {
        /** @var string $first */
        $first = array_shift($strings);
        $first = $this->normalizePath($first);

        $this->lastSlashPosition = null;

        for ($i = 0; $i < strlen($first); ++$i) {
            foreach ($strings as $string) {
                $string = $this->normalizePath($string);

                $sBackPosition = strlen($string) - $i - 1;
                $firstBackPosition = strlen($first) - $i - 1;

                if ($this->shouldIncludeChar($string, $sBackPosition, $first, $firstBackPosition, $i)) {
                    continue;
                }

                if ($this->lastSlashPosition !== null) {
                    return substr($first, -(int) $this->lastSlashPosition);
                }

                return substr($first, -$i);
            }
        }

        return $first;
    }

    public function subtractFromRight(string $mainString, string $stringToSubtract): string
    {
        return Strings::substring($mainString, 0, -strlen($stringToSubtract));
    }

    public function subtractFromLeft(string $mainString, string $stringToSubtract): string
    {
        return Strings::substring($mainString, strlen($stringToSubtract));
    }

    private function normalizePath(string $firstString): string
    {
        return Strings::replace($firstString, '#\\\\#', '/');
    }

    private function shouldIncludeChar(
        string $string,
        int $sBackPosition,
        string $first,
        int $firstBackPosition,
        int $i
    ): bool {
        if (! isset($string[$sBackPosition])) {
            return false;
        }

        if ($first[$firstBackPosition] !== $string[$sBackPosition]) {
            return false;
        }

        if ($string[$sBackPosition] === '/') {
            $this->lastSlashPosition = $i;
        }

        return true;
    }
}

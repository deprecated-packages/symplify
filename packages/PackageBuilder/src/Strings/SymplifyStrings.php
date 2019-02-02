<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Strings;

final class SymplifyStrings
{
    /**
     * @param int|string|float|bool|null ...$ars
     */
    public function sprintf(string $format, ...$ars): string
    {
        // @todo validate argument number - e.g. %s, [1, 2] should fail
        return sprintf($format, ...$ars);
    }
}

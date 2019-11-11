<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

final class EolConfiguration
{
    /**
     * @var string
     */
    private static $eolChar = "\n";

    public static function getEolChar(): string
    {
        return self::$eolChar;
    }

    public static function setEolChar(string $eolChar): void
    {
        self::$eolChar = $eolChar;
    }
}

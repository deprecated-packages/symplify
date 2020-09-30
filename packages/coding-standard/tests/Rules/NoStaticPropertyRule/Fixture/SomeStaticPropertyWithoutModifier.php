<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

final class SomeStaticPropertyWithoutModifier
{
    private $nonStaticFileNames = [];
    static $customFileNames = [];

    public function getNonStaticFileNames(): array
    {
        return $this->nonStaticFileNames;
    }

    public static function getCustomFileNames(): array
    {
        return self::$customFileNames;
    }
}

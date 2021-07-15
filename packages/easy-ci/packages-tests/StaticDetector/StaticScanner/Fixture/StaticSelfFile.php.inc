<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\StaticDetector\StaticScanner\Fixture;

final class StaticSelfFile
{
    public static function staticClassMethod(): void
    {
    }

    public function run()
    {
        self::staticClassMethod();
    }
}

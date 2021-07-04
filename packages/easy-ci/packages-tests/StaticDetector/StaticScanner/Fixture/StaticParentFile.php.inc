<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\StaticDetector\StaticScanner\Fixture;

final class StaticParentFile extends AbstractSomeParentClassWithStaticMethod
{
    public function staticClassMethod(): void
    {
        parent::mama();
    }
}

abstract class AbstractSomeParentClassWithStaticMethod
{
    public static function mama(): void
    {
    }
}

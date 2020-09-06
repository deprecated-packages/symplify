<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ExcessivePublicCountRule\Fixture;

final class SkipConstructorAndMagicMethods
{
    public $firstProperty;

    public $secondProperty;

    public function __construct()
    {
    }

    public function __clone()
    {
    }

    public function __toString()
    {
        return 'you';
    }

    public function __invoke()
    {
    }
}

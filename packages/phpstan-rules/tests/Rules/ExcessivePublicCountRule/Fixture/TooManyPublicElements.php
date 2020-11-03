<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExcessivePublicCountRule\Fixture;

final class TooManyPublicElements
{
    public const NAME = 'value';

    public const NAME_2 = 'value_2';

    public const NAME_3 = 'value_3';

    public $value;

    public $value2;

    public $value3;
}

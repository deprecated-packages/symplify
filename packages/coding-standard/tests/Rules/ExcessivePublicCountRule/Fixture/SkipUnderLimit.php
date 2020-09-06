<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ExcessivePublicCountRule\Fixture;

final class SkipUnderLimit
{
    public const NAME = 'value';

    protected const NAME_2 = 'value_2';

    private const NAME_3 = 'value_3';

    public $value;

    protected $value2;

    private $value3;
}

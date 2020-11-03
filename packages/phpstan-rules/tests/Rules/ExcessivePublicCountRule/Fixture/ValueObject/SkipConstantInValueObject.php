<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExcessivePublicCountRule\Fixture\ValueObject;

final class SkipConstantInValueObject
{
    public const NAME = 'value';

    public const NAME_2 = 'value_2';

    public const NAME_3 = 'value_3';

    public const NAME_4 = 'value_4';

    public const NAME_5 = 'value_5';
}

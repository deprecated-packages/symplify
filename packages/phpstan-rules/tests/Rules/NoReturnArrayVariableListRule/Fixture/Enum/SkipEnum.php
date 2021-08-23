<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule\Fixture\Enum;

/**
 * @enum
 */
final class SkipEnum
{
    public const YES = 'yes';
    public const NO = 'no';

    public static function getValues()
    {
        return [
            self::YES => 'Yes',
            self::NO => 'No',
        ];
    }
}

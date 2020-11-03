<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Fixture;

use stdClass;

final class ParamArrayStringObject
{
    /**
     * @param array<string, stdClass> $values
     */
    public function run(array $values)
    {
        return $values;
    }
}

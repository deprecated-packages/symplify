<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoArrayStringObjectReturnRule\Fixture;

use stdClass;

final class PropertyArrayStringObject
{
    /**
     * @var array<string, stdClass>
     */
    private $values;

    public function run()
    {
        foreach ($this->values as $key => $value) {
        }
    }
}

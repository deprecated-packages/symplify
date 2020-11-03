<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoArrayStringObjectReturnRule\Fixture;

use stdClass;

final class ArrayStringObjectReturn
{
    /**
     * @var array<string, stdClass>
     */
    private $values;

    public function run()
    {
        return $this->getValues();
    }

    /**
     * @return array<string, stdClass>
     */
    private function getValues()
    {
        return $this->values;
    }
}

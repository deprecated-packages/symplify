<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Fixture;

use stdClass;

final class SkipNonStringKey
{
    /**
     * @var array<int, stdClass>
     */
    private $values;

    public function run()
    {
        return $this->getValues();
    }

    /**
     * @return array<int, stdClass>
     */
    private function getValues()
    {
        return $this->values;
    }
}

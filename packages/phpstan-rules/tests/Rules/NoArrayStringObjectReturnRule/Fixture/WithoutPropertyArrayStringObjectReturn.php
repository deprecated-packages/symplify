<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Fixture;

use stdClass;

final class WithoutPropertyArrayStringObjectReturn
{
    public function run()
    {
        return $this->getValues();
    }

    /**
     * @return array<string, stdClass>
     */
    private function getValues()
    {
        // ...
    }
}

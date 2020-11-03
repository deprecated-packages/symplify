<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReferenceRule\Fixture;

final class ReferenceArgument
{
    public function go($argument)
    {
        $this->run([&$argument]);
    }

    private function run(array $array)
    {
    }
}

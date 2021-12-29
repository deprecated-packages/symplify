<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipNullCompare
{
    private $value;

    public function run()
    {
        if ($this->value === null) {
            $this->value = 100;
        }

        return $this->value;
    }
}

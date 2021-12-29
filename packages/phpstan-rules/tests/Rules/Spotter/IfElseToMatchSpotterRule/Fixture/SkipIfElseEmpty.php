<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipIfElseEmpty
{
    private $values;

    public function run($values, $noCache)
    {
        if (empty($this->values) || $noCache) {
            $this->values = 100;
        }

        return $this->values;
    }
}

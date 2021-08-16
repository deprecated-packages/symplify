<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class TranslateFunction
{
    public function run($message)
    {
        $this->go($message, 333, [333]);
    }

    public function go($value, $anotherValue, $next)
    {
    }
}

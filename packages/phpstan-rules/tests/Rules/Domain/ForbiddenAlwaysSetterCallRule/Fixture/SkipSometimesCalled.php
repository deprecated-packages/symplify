<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source\FirstClassIdea;

final class SkipSometimesCalled
{
    public function run()
    {
        $firstIdea = new FirstClassIdea();
        $firstIdea->addMotivation(1000);

        // there is no setter now, to it is optional
        $secondIdea = new FirstClassIdea();
    }
}

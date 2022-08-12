<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source\FirstClassIdea;

final class AlwaysTheSame
{
    public function setterRun()
    {
        $firstIdea = new FirstClassIdea();
        $firstIdea->addMotivation(1000);

        $secondIdea = new FirstClassIdea();
        $secondIdea->addMotivation(50);
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source\FirstClassIdea;

final class SkipConstructorPassed
{
    public function setterRun()
    {
        $firstIdea = new FirstClassIdea(1000);

        $secondIdea = new FirstClassIdea(50);
    }
}

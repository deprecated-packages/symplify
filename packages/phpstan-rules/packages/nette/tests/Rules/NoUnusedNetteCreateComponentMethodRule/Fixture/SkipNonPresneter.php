<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoUnusedNetteCreateComponentMethodRule\Fixture;

final class SkipNonPresneter
{
    protected function createComponentWhatever()
    {
    }
}

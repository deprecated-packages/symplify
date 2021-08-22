<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteArrayAccessInControlRule\Fixture;

final class SkipDimFetchOutsideNette
{
    public function someAction()
    {
        return $this['some'];
    }
}

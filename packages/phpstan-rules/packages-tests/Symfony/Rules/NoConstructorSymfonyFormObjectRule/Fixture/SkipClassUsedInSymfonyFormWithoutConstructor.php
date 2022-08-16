<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\NoConstructorSymfonyFormObjectRule\Fixture;

final class SkipClassUsedInSymfonyFormWithoutConstructor
{
    private int $onlyOptionalValue;

    public function __construct()
    {
        $this->onlyOptionalValue = 100;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\Fixture;

interface SkipInterfaceContractGetter
{
    public function getName(): string;
}

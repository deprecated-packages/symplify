<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture;

final class LocallyUsedPublicConstantByName
{
    public const LOCALLY_ONLY_NAMED = 'public is not correct';

    public function run()
    {
        return LocallyUsedPublicConstantByName::LOCALLY_ONLY_NAMED;
    }
}

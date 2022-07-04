<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Source\Contract\MethodRequiredInterface;

final class InterfaceWithExtraMethod implements MethodRequiredInterface
{
    public function useMeMaybe()
    {
    }

    public function extraMethod()
    {
    }
}

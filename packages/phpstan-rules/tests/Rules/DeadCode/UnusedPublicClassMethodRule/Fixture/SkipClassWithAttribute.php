<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

final class SkipClassWithAttribute
{
    #[Required]
    public function autowire()
    {
    }
}

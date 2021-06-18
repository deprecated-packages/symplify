<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

final class WithRequiredAttributeNotAutowire
{
    #[Required]
    public function run()
    {
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule\Fixture;

use Nette\DI\Attributes\Inject;

final class WithInjectAttribute
{
    #[Inject]
    public function run()
    {
    }
}

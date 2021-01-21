<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

final class ThisArgument
{
    public function run()
    {
        $this->service->execute($this);
    }
}

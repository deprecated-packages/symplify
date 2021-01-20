<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

final class SkipNotThis
{
    public function run()
    {
        $this->service->execute(Foo::class);
    }
}

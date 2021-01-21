<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

final class ThisArgumentCopy
{
    public function run()
    {
        $self = $this;
        $this->service->execute($self);
    }
}

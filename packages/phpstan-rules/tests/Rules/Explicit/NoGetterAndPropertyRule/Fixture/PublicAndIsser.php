<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoGetterAndPropertyRule\Fixture;

final class PublicAndIsser
{
    public $enabled;

    public function isEnabled()
    {
        return $this->isEnabled();
    }
}

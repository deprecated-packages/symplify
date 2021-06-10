<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

final class SkipEmptyReturn
{
    private $name;

    public function setName(string $name): void
    {
        if ($this->name === 'hey') {
            return;
        }

        $this->name = $name;
    }
}

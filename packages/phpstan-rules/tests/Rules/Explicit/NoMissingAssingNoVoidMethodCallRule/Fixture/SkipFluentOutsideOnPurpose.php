<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

final class SkipFluentOutsideOnPurpose
{
    public function run()
    {
        $this->setName('name');
    }

    private function setName(string $name): self
    {
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

final class SkipScalar
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function another($another)
    {
        $another->run($this->key);
    }
}

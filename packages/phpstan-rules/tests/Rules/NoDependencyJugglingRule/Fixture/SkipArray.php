<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Source\JuggleBall;

final class SkipArray
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    public function another($another)
    {
        $another->run($this->configs);
    }
}

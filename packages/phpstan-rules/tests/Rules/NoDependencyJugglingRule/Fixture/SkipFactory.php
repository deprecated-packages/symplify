<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Source\JuggleBall;

final class SkipFactory
{
    /**
     * @var JuggleBall
     */
    private $juggleBall;

    public function __construct(JuggleBall $juggleBall)
    {
        $this->juggleBall = $juggleBall;
    }

    public function create($another)
    {
        $another->run($this->juggleBall);
    }
}

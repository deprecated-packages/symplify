<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

use stdClass;
use Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Source\JuggleBall;

final class SkipFactoryMethod
{
    /**
     * @var JuggleBall
     */
    private $juggleBall;

    public function __construct(JuggleBall $juggleBall)
    {
        $this->juggleBall = $juggleBall;
    }

    public function create()
    {
        $another = new stdClass();
        $another->run($this->juggleBall);
        return $another;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Source\JuggleBall;

final class SkipPrivatesCaller
{
    /**
     * @var JuggleBall
     */
    private $juggleBall;

    public function __construct(JuggleBall $juggleBall)
    {
        $this->juggleBall = $juggleBall;
    }

    public function another(PrivatesCaller $privatesCaller, PrivatesAccessor $privatesAccessor)
    {
        $privatesCaller->callPrivateMethod($this->juggleBall, 'hey');

        $privatesAccessor->getPrivateProperty($this->juggleBall, 'hey');
    }
}

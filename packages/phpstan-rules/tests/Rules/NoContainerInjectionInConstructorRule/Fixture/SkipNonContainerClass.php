<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoContainerInjectionInConstructorRule\Fixture;

use Nette\Utils\Strings;

final class SkipNonContainerClass
{
    /**
     * @var Strings
     */
    private $strings;

    public function __construct(Strings $strings)
    {
        $this->strings = $strings;
    }
}
